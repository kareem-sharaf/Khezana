# 📐 Read Layer Design - Khezana Marketplace
## تصميم طبقة القراءة - CQRS Light Architecture

**الإصدار:** 1.0  
**التاريخ:** 2026-01-20  
**الغرض:** Blueprint نهائي لتنفيذ Read Layer وفق CQRS Light  
**المرجع الإلزامي:** هذا المستند هو المرجع الوحيد لأي تنفيذ Controllers / API لاحقاً

---

## 📌 جدول المحتويات

1. [Overview - نظرة عامة](#1-overview)
2. [Folder Structure - هيكل المجلدات](#2-folder-structure)
3. [Query Objects - كائنات الاستعلام](#3-query-objects)
4. [Read Models - نماذج القراءة](#4-read-models)
5. [Caching Strategy - استراتيجية التخزين المؤقت](#5-caching-strategy)
6. [Error Strategy - استراتيجية الأخطاء](#6-error-strategy)
7. [Naming Conventions - قواعد التسمية](#7-naming-conventions)

---

## 1. Overview - نظرة عامة

### 1.1 الهدف

**Read Layer** هو طبقة منفصلة تماماً عن Business Logic (Write Layer) مخصصة للقراءة فقط (Read-Only) وفق مبادئ CQRS Light.

### 1.2 المبادئ الأساسية

- ✅ **Read-Only**: لا تعديلات، لا كتابة، لا حذف
- ✅ **Separation of Concerns**: فصل كامل عن Actions / Services / Policies
- ✅ **Query Objects**: كل Use Case = Query Object مستقل
- ✅ **Read Models**: DTOs/View Models صريحة للعرض
- ✅ **Performance First**: Eager Loading، Caching، N+1 Prevention
- ✅ **No Business Logic**: لا Guards، لا Validations، لا Side Effects

### 1.3 الفصل عن Write Layer

**⚠️ قاعدة إلزامية:**
- Read Layer **لا يستدعي** أي Action
- Read Layer **لا يستدعي** أي Service للكتابة
- Read Layer **لا يستخدم** أي Policy
- Read Layer **لا يستخدم** أي Domain Guard
- Read Layer **يستخدم فقط** Eloquent Models + Query Builders

### 1.4 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `PUBLIC_READ_FLOW.md` - قواعد الرؤية والاستعلام
- `BUSINESS_FLOW.md` - Business Rules (للإشارة فقط، لا للتنفيذ)

---

## 2. Folder Structure - هيكل المجلدات

### 2.1 الهيكل المقترح

```
app/
└── Read/
    ├── Items/
    │   ├── Queries/
    │   │   ├── BrowseItemsQuery.php
    │   │   ├── ViewItemQuery.php
    │   │   └── SearchItemsQuery.php
    │   └── Models/
    │       └── ItemReadModel.php
    │
    ├── Requests/
    │   ├── Queries/
    │   │   ├── BrowseRequestsQuery.php
    │   │   ├── ViewRequestQuery.php
    │   │   └── SearchRequestsQuery.php
    │   └── Models/
    │       └── RequestReadModel.php
    │
    ├── Offers/
    │   ├── Queries/
    │   │   └── RequestOffersQuery.php
    │   └── Models/
    │       └── OfferReadModel.php
    │
    └── Shared/
        ├── Filters/
        │   ├── ItemFilters.php
        │   └── RequestFilters.php
        └── Exceptions/
            ├── NotFoundException.php
            └── ForbiddenException.php
```

### 2.2 مسؤولية كل Folder

#### 2.2.1 `app/Read/Items/`

**المسؤولية:**
- Query Objects للـ Items (Browse, View, Search)
- Read Models للـ Items (ItemReadModel)

**المحتوى:**
- `Queries/`: Query Objects للـ Items
- `Models/`: Read Models للـ Items

---

#### 2.2.2 `app/Read/Requests/`

**المسؤولية:**
- Query Objects للـ Requests (Browse, View, Search)
- Read Models للـ Requests (RequestReadModel)

**المحتوى:**
- `Queries/`: Query Objects للـ Requests
- `Models/`: Read Models للـ Requests

---

#### 2.2.3 `app/Read/Offers/`

**المسؤولية:**
- Query Objects للـ Offers (RequestOffersQuery فقط)
- Read Models للـ Offers (OfferReadModel)

**المحتوى:**
- `Queries/`: Query Objects للـ Offers
- `Models/`: Read Models للـ Offers

**⚠️ ملاحظة:** Offers لا تحتوي على Browse أو Search لأنها غير مرئية للعامة.

---

#### 2.2.4 `app/Read/Shared/`

**المسؤولية:**
- Shared utilities للـ Read Layer
- Filters (Query Filters)
- Exceptions (Custom Exceptions)

**المحتوى:**
- `Filters/`: Query Filters (reusable)
- `Exceptions/`: Custom Exceptions للـ Read Layer

---

### 2.3 الفصل عن Write Layer

**Write Layer (موجود):**
```
app/
├── Actions/          # Write Actions
├── Services/        # Write Services
├── Policies/        # Authorization
└── Models/          # Eloquent Models (shared)
```

**Read Layer (جديد):**
```
app/
└── Read/            # Read-Only Layer
```

**⚠️ قاعدة:** Read Layer **لا يلمس** أي شيء في Write Layer.

---

## 3. Query Objects - كائنات الاستعلام

### 3.1 Query Object Pattern

**كل Query Object:**
- Class مستقل
- Method واحد: `execute()`
- Input: Parameters (filters, sorting, pagination)
- Output: Collection / Model / Paginator
- Responsibility: بناء Query فقط (لا Business Logic)

---

### 3.2 Items Query Objects

#### 3.2.1 BrowseItemsQuery

**Class:** `App\Read\Items\Queries\BrowseItemsQuery`

**Purpose:** عرض قائمة Items المعتمدة والمتاحة

**Input Parameters:**
```php
class BrowseItemsQuery
{
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        // $filters = [
        //     'operation_type' => 'sell' | 'rent' | 'donate' | null,
        //     'category_id' => int | null,
        //     'price_min' => float | null,
        //     'price_max' => float | null,
        // ]
        // $sort = 'created_at_desc' | 'price_asc' | 'price_desc' | 'title_asc' | 'title_desc' | 'updated_at_desc'
        // $page = int (default: 1)
        // $perPage = int (default: 20, max: 50)
    }
}
```

**Query Logic:**
```php
Item::query()
    // Visibility Rules (PUBLIC_READ_FLOW.md Section 2.1.1)
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) {
        $q->where('availability_status', ItemAvailability::AVAILABLE)
          ->orWhere('is_available', true); // Fallback
    })
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
    
    // Filters (optional)
    ->when(isset($filters['operation_type']), fn($q) => $q->where('operation_type', $filters['operation_type']))
    ->when(isset($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
    ->when(isset($filters['price_min']), fn($q) => $q->where('price', '>=', $filters['price_min']))
    ->when(isset($filters['price_max']), fn($q) => $q->where('price', '<=', $filters['price_max']))
    
    // Sorting
    ->when($sort === 'price_asc', fn($q) => $q->orderBy('price', 'asc'))
    ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
    ->when($sort === 'title_asc', fn($q) => $q->orderBy('title', 'asc'))
    ->when($sort === 'title_desc', fn($q) => $q->orderBy('title', 'desc'))
    ->when($sort === 'updated_at_desc', fn($q) => $q->orderBy('updated_at', 'desc'))
    ->default(fn($q) => $q->orderBy('created_at', 'desc'))
    
    // Select specific columns (Performance)
    ->select('id', 'title', 'description', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at')
    
    // Eager Loading (PUBLIC_READ_FLOW.md Section 6.1.1)
    ->with([
        'user:id,name',
        'category:id,name,slug',
        'images' => fn($q) => $q->select('id,item_id,path,is_primary')
                               ->orderBy('is_primary', 'desc')
                               ->limit(1), // Primary image only
        'approval:id,approvable_type,approvable_id,status'
    ])
    
    // Pagination
    ->paginate(min($perPage, 50), ['*'], 'page', $page);
```

**Edge Cases:**
- ✅ Empty results → Return empty paginator (لا error)
- ✅ Invalid filters → Ignore invalid filters (لا error)
- ✅ Invalid page number → Return page 1
- ✅ Invalid sort → Use default sort

**Return Type:** `Illuminate\Contracts\Pagination\LengthAwarePaginator`

---

#### 3.2.2 ViewItemQuery

**Class:** `App\Read\Items\Queries\ViewItemQuery`

**Purpose:** عرض تفاصيل Item واحد

**Input Parameters:**
```php
class ViewItemQuery
{
    public function execute(int $itemId, ?string $slug = null, ?User $user = null): ?ItemReadModel
    {
        // $itemId = int (required)
        // $slug = string | null (optional, for validation)
        // $user = User | null (optional, for owner exception)
    }
}
```

**Query Logic:**
```php
$item = Item::query()
    ->where('id', $itemId)
    
    // Visibility Rules (PUBLIC_READ_FLOW.md Section 2.1.2)
    ->where(function($q) use ($user) {
        // Public visibility
        $q->whereHas('approval', fn($a) => $a->where('status', ApprovalStatus::APPROVED))
          ->where(function($av) {
              $av->where('availability_status', ItemAvailability::AVAILABLE)
                 ->orWhere('is_available', true);
          })
          ->whereNull('deleted_at')
          ->whereNull('archived_at');
        
        // Owner exception (إذا كان User مسجل)
        if ($user) {
            $q->orWhere('user_id', $user->id);
        }
    })
    
    // Slug validation (if provided)
    ->when($slug, fn($q) => $q->where('slug', $slug))
    
    // Select specific columns
    ->select('id', 'title', 'description', 'price', 'deposit_amount', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at', 'updated_at')
    
    // Eager Loading (PUBLIC_READ_FLOW.md Section 4.1.2)
    ->with([
        'user:id,name,created_at',
        'category:id,name,slug,description',
        'images' => fn($q) => $q->select('id,item_id,path,is_primary,alt')
                               ->orderBy('is_primary', 'desc'),
        'itemAttributes.attribute:id,name,type', // Nested eager loading (max depth: 2)
        'approval:id,approvable_type,approvable_id,status,reviewed_at'
    ])
    
    ->first();
```

**Edge Cases:**
- ❌ Item غير موجود → Return `null` (Controller يرمي 404)
- ❌ Item غير Approved → Return `null` (إلا إذا كان Owner)
- ❌ Item Approved لكن Unavailable → Return `null` (إلا إذا كان Owner)
- ❌ Slug غير متطابق → Return `null` (Controller يرمي 301 Redirect)

**Return Type:** `?ItemReadModel` (null إذا غير موجود)

---

#### 3.2.3 SearchItemsQuery

**Class:** `App\Read\Items\Queries\SearchItemsQuery`

**Purpose:** البحث في Items

**Input Parameters:**
```php
class SearchItemsQuery
{
    public function execute(string $query, ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        // $query = string (required, minimum 2 characters)
        // $sort = 'relevance' | 'created_at_desc' | null
        // $page = int (default: 1)
        // $perPage = int (default: 20, max: 50)
    }
}
```

**Query Logic:**
```php
// Validate query length
if (strlen(trim($query)) < 2) {
    return new LengthAwarePaginator([], 0, $perPage, $page);
}

Item::query()
    // Visibility Rules (same as BrowseItemsQuery)
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) {
        $q->where('availability_status', ItemAvailability::AVAILABLE)
          ->orWhere('is_available', true);
    })
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
    
    // Full-text search (PUBLIC_READ_FLOW.md Section 4.1.3)
    ->where(function($q) use ($query) {
        $q->where('title', 'LIKE', "%{$query}%")
          ->orWhere('description', 'LIKE', "%{$query}%")
          ->orWhereHas('category', fn($cat) => $cat->where('name', 'LIKE', "%{$query}%"));
    })
    
    // Sorting
    ->when($sort === 'created_at_desc', fn($q) => $q->orderBy('created_at', 'desc'))
    ->default(fn($q) => $q->orderBy('created_at', 'desc')) // Fallback if relevance not available
    
    // Select + Eager Loading (same as BrowseItemsQuery)
    ->select('id', 'title', 'description', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at')
    ->with([
        'user:id,name',
        'category:id,name,slug',
        'images' => fn($q) => $q->select('id,item_id,path,is_primary')->orderBy('is_primary', 'desc')->limit(1),
        'approval:id,approvable_type,approvable_id,status'
    ])
    
    ->paginate(min($perPage, 50), ['*'], 'page', $page);
```

**Edge Cases:**
- ✅ Query < 2 characters → Return empty paginator (لا error)
- ✅ No results → Return empty paginator (لا error)

**Return Type:** `Illuminate\Contracts\Pagination\LengthAwarePaginator`

---

### 3.3 Requests Query Objects

#### 3.3.1 BrowseRequestsQuery

**Class:** `App\Read\Requests\Queries\BrowseRequestsQuery`

**Purpose:** عرض قائمة Requests المعتمدة

**Input Parameters:**
```php
class BrowseRequestsQuery
{
    public function execute(array $filters = [], ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        // $filters = [
        //     'status' => 'open' | 'fulfilled' | 'closed' | null,
        //     'category_id' => int | null,
        // ]
        // $sort = 'created_at_desc' | 'status_asc' | 'status_desc' | 'title_asc' | 'title_desc' | 'updated_at_desc'
        // $page = int (default: 1)
        // $perPage = int (default: 20, max: 50)
    }
}
```

**Query Logic:**
```php
Request::query()
    // Visibility Rules (PUBLIC_READ_FLOW.md Section 2.2.1)
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
    
    // Filters (optional)
    ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
    ->when(isset($filters['category_id']), fn($q) => $q->where('category_id', $filters['category_id']))
    
    // Sorting
    ->when($sort === 'status_asc', fn($q) => $q->orderBy('status', 'asc'))
    ->when($sort === 'status_desc', fn($q) => $q->orderBy('status', 'desc'))
    ->when($sort === 'title_asc', fn($q) => $q->orderBy('title', 'asc'))
    ->when($sort === 'title_desc', fn($q) => $q->orderBy('title', 'desc'))
    ->when($sort === 'updated_at_desc', fn($q) => $q->orderBy('updated_at', 'desc'))
    ->default(fn($q) => $q->orderBy('created_at', 'desc'))
    
    // Select specific columns
    ->select('id', 'title', 'description', 'status', 'user_id', 'category_id', 'created_at', 'updated_at')
    
    // Eager Loading (PUBLIC_READ_FLOW.md Section 4.2.1)
    ->with([
        'user:id,name',
        'category:id,name,slug',
        'approval:id,approvable_type,approvable_id,status',
    ])
    ->withCount([
        'offers' => fn($q) => $q->where('status', OfferStatus::PENDING)
    ])
    
    ->paginate(min($perPage, 50), ['*'], 'page', $page);
```

**Edge Cases:**
- ✅ Empty results → Return empty paginator
- ✅ Invalid status filter → Ignore (لا error)

**Return Type:** `Illuminate\Contracts\Pagination\LengthAwarePaginator`

---

#### 3.3.2 ViewRequestQuery

**Class:** `App\Read\Requests\Queries\ViewRequestQuery`

**Purpose:** عرض تفاصيل Request واحد

**Input Parameters:**
```php
class ViewRequestQuery
{
    public function execute(int $requestId, ?string $slug = null, ?User $user = null): ?RequestReadModel
    {
        // $requestId = int (required)
        // $slug = string | null (optional, for validation)
        // $user = User | null (optional, for owner exception + offers visibility)
    }
}
```

**Query Logic:**
```php
$request = Request::query()
    ->where('id', $requestId)
    
    // Visibility Rules (PUBLIC_READ_FLOW.md Section 2.2.2)
    ->where(function($q) use ($user) {
        // Public visibility
        $q->whereHas('approval', fn($a) => $a->where('status', ApprovalStatus::APPROVED))
          ->whereNull('deleted_at')
          ->whereNull('archived_at');
        
        // Owner exception
        if ($user) {
            $q->orWhere('user_id', $user->id);
        }
    })
    
    // Slug validation (if provided)
    ->when($slug, fn($q) => $q->where('slug', $slug))
    
    // Select specific columns
    ->select('id', 'title', 'description', 'status', 'user_id', 'category_id', 'created_at', 'updated_at')
    
    // Eager Loading (PUBLIC_READ_FLOW.md Section 4.2.2)
    ->with([
        'user:id,name,created_at',
        'category:id,name,slug,description',
        'itemAttributes.attribute:id,name,type',
        'approval:id,approvable_type,approvable_id,status,reviewed_at',
        'offers' => function($q) use ($user) {
            // فقط للـ Request Owner أو Offer Owner (PUBLIC_READ_FLOW.md Section 2.3.2)
            if ($user) {
                $q->where(function($o) use ($user) {
                    $o->whereHas('request', fn($r) => $r->where('user_id', $user->id))
                      ->orWhere('user_id', $user->id);
                });
            } else {
                $q->whereRaw('1 = 0'); // لا Offers للـ Guests
            }
        } => [
            'user:id,name',
            'item:id,title,price,availability_status' => [
                'images' => fn($q) => $q->where('is_primary', true)->select('id,item_id,path')
            ],
            'request:id,title,status'
        ]
    ])
    
    ->first();
```

**Edge Cases:**
- ❌ Request غير موجود → Return `null`
- ❌ Request غير Approved → Return `null` (إلا إذا كان Owner)
- ⚠️ Offers: Guests لا يرون Offers (empty array في Read Model)

**Return Type:** `?RequestReadModel` (null إذا غير موجود)

---

#### 3.3.3 SearchRequestsQuery

**Class:** `App\Read\Requests\Queries\SearchRequestsQuery`

**Purpose:** البحث في Requests

**Input Parameters:**
```php
class SearchRequestsQuery
{
    public function execute(string $query, ?string $sort = null, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        // $query = string (required, minimum 2 characters)
        // $sort = 'relevance' | 'created_at_desc' | null
        // $page = int (default: 1)
        // $perPage = int (default: 20, max: 50)
    }
}
```

**Query Logic:**
```php
// Validate query length
if (strlen(trim($query)) < 2) {
    return new LengthAwarePaginator([], 0, $perPage, $page);
}

Request::query()
    // Visibility Rules (same as BrowseRequestsQuery)
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
    
    // Full-text search
    ->where(function($q) use ($query) {
        $q->where('title', 'LIKE', "%{$query}%")
          ->orWhere('description', 'LIKE', "%{$query}%")
          ->orWhereHas('category', fn($cat) => $cat->where('name', 'LIKE', "%{$query}%"));
    })
    
    // Sorting
    ->when($sort === 'created_at_desc', fn($q) => $q->orderBy('created_at', 'desc'))
    ->default(fn($q) => $q->orderBy('created_at', 'desc'))
    
    // Select + Eager Loading (same as BrowseRequestsQuery)
    ->select('id', 'title', 'description', 'status', 'user_id', 'category_id', 'created_at')
    ->with([
        'user:id,name',
        'category:id,name,slug',
        'approval:id,approvable_type,approvable_id,status',
    ])
    ->withCount([
        'offers' => fn($q) => $q->where('status', OfferStatus::PENDING)
    ])
    
    ->paginate(min($perPage, 50), ['*'], 'page', $page);
```

**Edge Cases:**
- ✅ Query < 2 characters → Return empty paginator
- ✅ No results → Return empty paginator

**Return Type:** `Illuminate\Contracts\Pagination\LengthAwarePaginator`

---

### 3.4 Offers Query Objects

#### 3.4.1 RequestOffersQuery

**Class:** `App\Read\Offers\Queries\RequestOffersQuery`

**Purpose:** عرض العروض على Request (محدود)

**Input Parameters:**
```php
class RequestOffersQuery
{
    public function execute(int $requestId, ?User $user = null): Collection
    {
        // $requestId = int (required)
        // $user = User | null (required for visibility - Guests cannot see offers)
    }
}
```

**Query Logic:**
```php
// Guests cannot see offers
if (!$user) {
    return collect([]);
}

Offer::query()
    ->where('request_id', $requestId)
    
    // Visibility Rules (PUBLIC_READ_FLOW.md Section 2.3.1)
    ->whereHas('request.approval', fn($a) => $a->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) use ($user) {
        // Request Owner يرى جميع Offers
        $q->whereHas('request', fn($r) => $r->where('user_id', $user->id))
          // Offer Owner يرى Offer الخاص به فقط
          ->orWhere('user_id', $user->id);
    })
    
    // Select specific columns
    ->select('id', 'request_id', 'user_id', 'item_id', 'operation_type', 'price', 'deposit_amount', 'status', 'message', 'created_at', 'updated_at')
    
    // Eager Loading (PUBLIC_READ_FLOW.md Section 4.2.3)
    ->with([
        'user:id,name',
        'item:id,title,price,availability_status' => [
            'images' => fn($q) => $q->where('is_primary', true)->select('id,item_id,path')
        ],
        'request:id,title,status'
    ])
    
    ->orderBy('created_at', 'desc')
    ->get();
```

**Edge Cases:**
- ❌ Request غير موجود → Return empty collection (Controller يرمي 404)
- ❌ User ليس Owner → Return empty collection (Controller يرمي 403 أو empty)
- ✅ No offers → Return empty collection

**Return Type:** `Illuminate\Support\Collection<OfferReadModel>`

---

## 4. Read Models - نماذج القراءة

### 4.1 Read Model Pattern

**كل Read Model:**
- Class مستقل (DTO/View Model)
- يحتوي فقط الحقول المسموح عرضها
- مسؤول عن Formatting فقط (price, dates, status labels)
- لا Business Logic
- Immutable (لا setters)

---

### 4.2 ItemReadModel

**Class:** `App\Read\Items\Models\ItemReadModel`

**Purpose:** DTO/View Model للـ Item

**Properties:**
```php
class ItemReadModel
{
    public readonly int $id;
    public readonly string $title;
    public readonly string $description;
    public readonly ?float $price; // null for DONATE
    public readonly ?float $depositAmount; // null for SELL/DONATE
    public readonly string $operationType; // 'sell' | 'rent' | 'donate'
    public readonly string $operationTypeLabel; // Formatted label
    public readonly string $availabilityStatus; // 'available' | 'unavailable'
    public readonly string $availabilityStatusLabel; // Formatted label
    public readonly string $slug; // Generated from title
    public readonly string $url; // Full URL: /items/{id}/{slug}
    
    // Relations (Read Models)
    public readonly ?UserReadModel $user;
    public readonly ?CategoryReadModel $category;
    public readonly Collection $images; // Collection<ImageReadModel>
    public readonly ?ImageReadModel $primaryImage; // First image or null
    public readonly Collection $attributes; // Collection<AttributeReadModel>
    
    // Meta
    public readonly Carbon $createdAt;
    public readonly Carbon $updatedAt;
    public readonly string $createdAtFormatted; // "2 days ago"
    public readonly string $updatedAtFormatted; // "2 days ago"
    
    // SEO
    public readonly string $canonicalUrl;
    public readonly array $metaTags; // ['robots' => 'index, follow', 'og:title' => ...]
}
```

**Forbidden Properties (❌ لا تعرض):**
- `user.email`
- `user.phone`
- `approval.reviewed_by`
- `approval.rejection_reason`
- `deleted_at`
- `archived_at`

**Formatting Methods:**
```php
// Price formatting
public function getFormattedPrice(): ?string
{
    return $this->price ? number_format($this->price, 2) . ' ' . config('app.currency', 'SAR') : null;
}

// Operation type label
public function getOperationTypeLabel(): string
{
    return match($this->operationType) {
        'sell' => __('items.operation_type.sell'),
        'rent' => __('items.operation_type.rent'),
        'donate' => __('items.operation_type.donate'),
    };
}

// Availability label
public function getAvailabilityStatusLabel(): string
{
    return match($this->availabilityStatus) {
        'available' => __('items.availability.available'),
        'unavailable' => __('items.availability.unavailable'),
    };
}
```

**Factory Method:**
```php
public static function fromModel(Item $item): self
{
    return new self(
        id: $item->id,
        title: $item->title,
        description: $item->description,
        price: $item->price,
        depositAmount: $item->deposit_amount,
        operationType: $item->operation_type->value,
        operationTypeLabel: $item->operation_type->label(),
        availabilityStatus: $item->availability_status->value ?? ($item->is_available ? 'available' : 'unavailable'),
        availabilityStatusLabel: $item->availability_status?->label() ?? ($item->is_available ? 'Available' : 'Unavailable'),
        slug: $item->slug ?? Str::slug($item->title),
        url: route('items.show', ['id' => $item->id, 'slug' => $item->slug ?? Str::slug($item->title)]),
        user: $item->user ? UserReadModel::fromModel($item->user) : null,
        category: $item->category ? CategoryReadModel::fromModel($item->category) : null,
        images: $item->images->map(fn($img) => ImageReadModel::fromModel($img)),
        primaryImage: $item->images->first() ? ImageReadModel::fromModel($item->images->first()) : null,
        attributes: $item->itemAttributes->map(fn($attr) => AttributeReadModel::fromModel($attr)),
        createdAt: $item->created_at,
        updatedAt: $item->updated_at,
        createdAtFormatted: $item->created_at->diffForHumans(),
        updatedAtFormatted: $item->updated_at->diffForHumans(),
        canonicalUrl: route('items.show', ['id' => $item->id, 'slug' => $item->slug ?? Str::slug($item->title)]),
        metaTags: [
            'robots' => 'index, follow',
            'og:type' => 'product',
            'og:title' => $item->title,
            'og:description' => Str::limit($item->description, 160),
            'og:image' => $item->images->first()?->path ?? config('app.default_image'),
        ],
    );
}
```

---

### 4.3 RequestReadModel

**Class:** `App\Read\Requests\Models\RequestReadModel`

**Purpose:** DTO/View Model للـ Request

**Properties:**
```php
class RequestReadModel
{
    public readonly int $id;
    public readonly string $title;
    public readonly string $description;
    public readonly string $status; // 'open' | 'fulfilled' | 'closed'
    public readonly string $statusLabel; // Formatted label
    public readonly string $slug;
    public readonly string $url;
    
    // Relations
    public readonly ?UserReadModel $user;
    public readonly ?CategoryReadModel $category;
    public readonly Collection $attributes;
    public readonly Collection $offers; // Collection<OfferReadModel> (empty for guests)
    public readonly int $offersCount; // Count of pending offers
    
    // Meta
    public readonly Carbon $createdAt;
    public readonly Carbon $updatedAt;
    public readonly string $createdAtFormatted;
    public readonly string $updatedAtFormatted;
    
    // SEO
    public readonly string $canonicalUrl;
    public readonly array $metaTags;
}
```

**Factory Method:**
```php
public static function fromModel(Request $request): self
{
    return new self(
        id: $request->id,
        title: $request->title,
        description: $request->description,
        status: $request->status->value,
        statusLabel: $request->status->label(),
        slug: $request->slug ?? Str::slug($request->title),
        url: route('requests.show', ['id' => $request->id, 'slug' => $request->slug ?? Str::slug($request->title)]),
        user: $request->user ? UserReadModel::fromModel($request->user) : null,
        category: $request->category ? CategoryReadModel::fromModel($request->category) : null,
        attributes: $request->itemAttributes->map(fn($attr) => AttributeReadModel::fromModel($attr)),
        offers: $request->offers->map(fn($offer) => OfferReadModel::fromModel($offer)),
        offersCount: $request->offers_count ?? $request->offers->where('status', OfferStatus::PENDING)->count(),
        createdAt: $request->created_at,
        updatedAt: $request->updated_at,
        createdAtFormatted: $request->created_at->diffForHumans(),
        updatedAtFormatted: $request->updated_at->diffForHumans(),
        canonicalUrl: route('requests.show', ['id' => $request->id, 'slug' => $request->slug ?? Str::slug($request->title)]),
        metaTags: [
            'robots' => 'index, follow',
            'og:type' => 'article',
            'og:title' => $request->title,
            'og:description' => Str::limit($request->description, 160),
        ],
    );
}
```

---

### 4.4 OfferReadModel

**Class:** `App\Read\Offers\Models\OfferReadModel`

**Purpose:** DTO/View Model للـ Offer

**Properties:**
```php
class OfferReadModel
{
    public readonly int $id;
    public readonly string $operationType;
    public readonly string $operationTypeLabel;
    public readonly ?float $price;
    public readonly ?float $depositAmount;
    public readonly string $status; // 'pending' | 'accepted' | 'rejected' | 'cancelled'
    public readonly string $statusLabel;
    public readonly ?string $message;
    
    // Relations
    public readonly ?UserReadModel $user; // Offer owner
    public readonly ?ItemReadModel $item; // If offer linked to item
    public readonly ?RequestReadModel $request; // Request this offer belongs to
    
    // Meta
    public readonly Carbon $createdAt;
    public readonly Carbon $updatedAt;
    public readonly string $createdAtFormatted;
    public readonly string $updatedAtFormatted;
}
```

**Factory Method:**
```php
public static function fromModel(Offer $offer): self
{
    return new self(
        id: $offer->id,
        operationType: $offer->operation_type->value,
        operationTypeLabel: $offer->operation_type->label(),
        price: $offer->price,
        depositAmount: $offer->deposit_amount,
        status: $offer->status->value,
        statusLabel: $offer->status->label(),
        message: $offer->message,
        user: $offer->user ? UserReadModel::fromModel($offer->user) : null,
        item: $offer->item ? ItemReadModel::fromModel($offer->item) : null,
        request: $offer->request ? RequestReadModel::fromModel($offer->request) : null,
        createdAt: $offer->created_at,
        updatedAt: $offer->updated_at,
        createdAtFormatted: $offer->created_at->diffForHumans(),
        updatedAtFormatted: $offer->updated_at->diffForHumans(),
    );
}
```

---

### 4.5 Shared Read Models

#### 4.5.1 UserReadModel

**Class:** `App\Read\Shared\Models\UserReadModel`

**Properties:**
```php
class UserReadModel
{
    public readonly int $id;
    public readonly string $name;
    public readonly Carbon $createdAt; // "Member since"
    public readonly string $memberSinceFormatted; // "Member since Jan 2024"
    
    // ❌ NO email
    // ❌ NO phone
    // ❌ NO password
}
```

---

#### 4.5.2 CategoryReadModel

**Class:** `App\Read\Shared\Models\CategoryReadModel`

**Properties:**
```php
class CategoryReadModel
{
    public readonly int $id;
    public readonly string $name;
    public readonly string $slug;
    public readonly ?string $description;
}
```

---

#### 4.5.3 ImageReadModel

**Class:** `App\Read\Shared\Models\ImageReadModel`

**Properties:**
```php
class ImageReadModel
{
    public readonly int $id;
    public readonly string $path; // Full URL
    public readonly bool $isPrimary;
    public readonly ?string $alt;
}
```

---

#### 4.5.4 AttributeReadModel

**Class:** `App\Read\Shared\Models\AttributeReadModel`

**Properties:**
```php
class AttributeReadModel
{
    public readonly int $id;
    public readonly string $name;
    public readonly string $type; // 'text' | 'number' | 'select'
    public readonly mixed $value; // Dynamic value
    public readonly string $formattedValue; // Formatted for display
}
```

---

## 5. Caching Strategy - استراتيجية التخزين المؤقت

### 5.1 Cache Layer Location

**Cache Logic:**
- **لا** يوضع داخل Query Objects
- **لا** يوضع داخل Read Models
- **يوضع** في Controller أو Middleware (لاحقاً)

**⚠️ ملاحظة:** Query Objects **لا تحتوي** على Cache Logic. Cache يتم تطبيقه في Layer أعلى (Controller/Middleware).

---

### 5.2 Cache Keys (Conceptual)

**كما في PUBLIC_READ_FLOW.md Section 6.6.1:**

**Browse Items:**
```
items:published:page:{page}:filters:{hash}
```

**Item Details:**
```
item:{id}:details
```

**Browse Requests:**
```
requests:published:page:{page}:filters:{hash}
```

**Request Details:**
```
request:{id}:details
```

**Search Results:**
```
items:search:query:{hash}:page:{page}
requests:search:query:{hash}:page:{page}
```

---

### 5.3 Cache TTL (Conceptual)

| الصفحة | TTL | ملاحظات |
|--------|-----|---------|
| Browse Items | 5 minutes | Frequently updated |
| Item Details | 10 minutes | Less frequently updated |
| Browse Requests | 5 minutes | Frequently updated |
| Request Details | 10 minutes | Less frequently updated |
| Search Results | 1 minute | Very dynamic |

---

### 5.4 Cache Invalidation Triggers (Conceptual)

**Cache **يجب** أن يتم invalidate عند:**

1. Item Approved → Invalidate `item:{id}:details` + `items:published:*`
2. Item Rejected/Archived → Invalidate `item:{id}:details` + `items:published:*`
3. Item availability_status changed → Invalidate `item:{id}:details` + `items:published:*`
4. Request Approved → Invalidate `request:{id}:details` + `requests:published:*`
5. Request Rejected/Archived → Invalidate `request:{id}:details` + `requests:published:*`
6. Request status changed → Invalidate `request:{id}:details` + `requests:published:*`

**⚠️ ملاحظة:** Cache Invalidation يتم عبر Events/Listeners في Write Layer (لاحقاً).

---

## 6. Error Strategy - استراتيجية الأخطاء

### 6.1 Custom Exceptions

#### 6.1.1 NotFoundException

**Class:** `App\Read\Shared\Exceptions\NotFoundException`

**Purpose:** Item/Request غير موجود أو غير مرئي

**Usage:**
```php
// في Controller
$item = app(BrowseItemsQuery::class)->execute($itemId, $slug, $user);
if (!$item) {
    throw new NotFoundException('Item not found or not visible.');
}
```

---

#### 6.1.2 ForbiddenException

**Class:** `App\Read\Shared\Exceptions\ForbiddenException`

**Purpose:** User ليس لديه صلاحية للوصول (مثل Offers)

**Usage:**
```php
// في Controller
$offers = app(RequestOffersQuery::class)->execute($requestId, $user);
if ($offers->isEmpty() && $user) {
    // Check if user is owner
    $request = Request::find($requestId);
    if (!$request || $request->user_id !== $user->id) {
        throw new ForbiddenException('You do not have permission to view these offers.');
    }
}
```

---

### 6.2 Return Strategy

#### 6.2.1 متى يرجع null؟

**Query Objects ترجع `null` في الحالات التالية:**

1. ✅ Item/Request غير موجود في Database
2. ✅ Item/Request غير Approved (إلا إذا كان Owner)
3. ✅ Item Approved لكن Unavailable (إلا إذا كان Owner)
4. ✅ Slug غير متطابق (Controller يرمي 301 Redirect)

**⚠️ قاعدة:** Query Objects **لا ترمي Exceptions**. Controller هو المسؤول عن رمي Exceptions.

---

#### 6.2.2 متى يرجع Empty Collection؟

**Query Objects ترجع Empty Collection في الحالات التالية:**

1. ✅ Browse/Search: No results found
2. ✅ Offers: User ليس Owner (Guests أو غير Owner)
3. ✅ Offers: No offers found

**⚠️ قاعدة:** Empty Collection **ليست** error. Controller يعرض "No results found".

---

#### 6.2.3 متى يرجع Empty Paginator؟

**Query Objects ترجع Empty Paginator في الحالات التالية:**

1. ✅ Browse/Search: No results found
2. ✅ Invalid filters → Ignored → No results

**⚠️ قاعدة:** Empty Paginator **ليست** error. Controller يعرض "No results found".

---

### 6.3 Error Handling Flow

```
Query Object
    ↓
Returns: Model | Collection | Paginator | null
    ↓
Controller
    ↓
Checks: null? → 404
        Empty? → Show "No results"
        Valid? → Return Read Model
    ↓
View / API Response
```

---

## 7. Naming Conventions - قواعد التسمية

### 7.1 Class Names

#### 7.1.1 Query Objects

**Pattern:** `{Entity}{Action}Query`

**Examples:**
- `BrowseItemsQuery`
- `ViewItemQuery`
- `SearchItemsQuery`
- `BrowseRequestsQuery`
- `ViewRequestQuery`
- `RequestOffersQuery`

**⚠️ قاعدة:** 
- Singular for single entity: `ViewItemQuery` (not `ViewItemsQuery`)
- Plural for collection: `BrowseItemsQuery` (not `BrowseItemQuery`)

---

#### 7.1.2 Read Models

**Pattern:** `{Entity}ReadModel`

**Examples:**
- `ItemReadModel`
- `RequestReadModel`
- `OfferReadModel`
- `UserReadModel`
- `CategoryReadModel`
- `ImageReadModel`
- `AttributeReadModel`

---

#### 7.1.3 Exceptions

**Pattern:** `{Type}Exception`

**Examples:**
- `NotFoundException`
- `ForbiddenException`

---

### 7.2 Method Names

#### 7.2.1 Query Objects

**Method:** `execute()`

**Parameters:**
- Filters: `array $filters = []`
- Sort: `?string $sort = null`
- Pagination: `int $page = 1, int $perPage = 20`
- Entity ID: `int $entityId`
- Slug: `?string $slug = null`
- User: `?User $user = null`

**Return Types:**
- Single entity: `?{Entity}ReadModel`
- Collection: `Collection<{Entity}ReadModel>`
- Pagination: `LengthAwarePaginator<{Entity}ReadModel>`

---

#### 7.2.2 Read Models

**Factory Method:** `fromModel()`

**Static Method:**
```php
public static function fromModel({Entity} $entity): self
```

**Formatting Methods:**
```php
public function getFormatted{Field}(): string
// Examples:
// getFormattedPrice()
// getFormattedDate()
// getOperationTypeLabel()
```

---

### 7.3 Folder Names

**Pattern:** `{Entity}` (PascalCase, Singular for folder, Plural for entity type)

**Examples:**
- `app/Read/Items/` (folder contains Item-related queries/models)
- `app/Read/Requests/` (folder contains Request-related queries/models)
- `app/Read/Offers/` (folder contains Offer-related queries/models)
- `app/Read/Shared/` (shared utilities)

---

### 7.4 File Names

**Pattern:** `{ClassName}.php`

**Examples:**
- `BrowseItemsQuery.php`
- `ItemReadModel.php`
- `NotFoundException.php`

---

### 7.5 Namespace Rules

**Pattern:** `App\Read\{Entity}\{Type}`

**Examples:**
- `App\Read\Items\Queries\BrowseItemsQuery`
- `App\Read\Items\Models\ItemReadModel`
- `App\Read\Shared\Exceptions\NotFoundException`

---

## 8. Summary - الملخص

### 8.1 القواعد الإلزامية

1. ✅ **Read-Only**: لا تعديلات على Business Logic
2. ✅ **Query Objects**: كل Use Case = Query Object مستقل
3. ✅ **Read Models**: DTOs/View Models صريحة
4. ✅ **No Business Logic**: لا Guards، لا Validations، لا Side Effects
5. ✅ **Performance**: Eager Loading، Select specific columns، No N+1
6. ✅ **Error Handling**: Query Objects ترجع null/empty، Controller يرمي Exceptions

### 8.2 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `PUBLIC_READ_FLOW.md` - قواعد الرؤية والاستعلام
- `BUSINESS_FLOW.md` - Business Rules (للإشارة فقط)

**⚠️ أي تنفيذ يجب أن يلتزم بهذا التصميم 100%.**

---

**الإصدار:** 1.0  
**آخر تحديث:** 2026-01-20  
**الحالة:** ✅ Approved for Implementation
