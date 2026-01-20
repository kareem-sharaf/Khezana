# 🎮 Controllers Layer Design - Khezana Marketplace
## تصميم طبقة Controllers - Thin Controllers Pattern

**الإصدار:** 1.0  
**التاريخ:** 2026-01-20  
**الغرض:** Blueprint نهائي لتنفيذ Controllers Layer وفق Thin Controllers Pattern  
**المرجع الإلزامي:** هذا المستند هو المرجع الوحيد لأي تنفيذ Controllers لاحقاً

---

## 📌 جدول المحتويات

1. [Overview - نظرة عامة](#1-overview)
2. [Folder Structure - هيكل المجلدات](#2-folder-structure)
3. [Controllers List - قائمة Controllers](#3-controllers-list)
4. [Method Signatures - توقيعات Methods](#4-method-signatures)
5. [Error Handling - معالجة الأخطاء](#5-error-handling)
6. [View Contracts - عقود Views](#6-view-contracts)
7. [Cache Integration - تكامل التخزين المؤقت](#7-cache-integration)

---

## 1. Overview - نظرة عامة

### 1.1 الهدف

**Controllers Layer** هو طبقة رقيقة (Thin Controllers) تعمل كـ Adapter بين HTTP Requests و Read Layer (Query Objects).

### 1.2 المبادئ الأساسية

- ✅ **Thin Controllers**: Controllers رقيقة جداً (Adapter فقط)
- ✅ **No Business Logic**: لا Business Logic داخل Controllers
- ✅ **No Eloquent**: لا استخدام مباشر لـ Eloquent Models
- ✅ **Query Objects Only**: الاعتماد فقط على Query Objects
- ✅ **Error Handling**: تطبيق Error Strategy حرفياً
- ✅ **Read-Only**: لا تعديلات، لا كتابة، لا حذف

### 1.3 الفصل عن Write Layer

**⚠️ قاعدة إلزامية:**
- Controllers **لا تستدعي** أي Action
- Controllers **لا تستدعي** أي Service للكتابة
- Controllers **لا تستخدم** أي Policy
- Controllers **لا تستخدم** أي Domain Guard
- Controllers **تستخدم فقط** Query Objects من Read Layer

### 1.4 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `READ_LAYER_DESIGN.md` - Query Objects و Error Strategy
- `PUBLIC_READ_FLOW.md` - URL Structure و SEO Rules

---

## 2. Folder Structure - هيكل المجلدات

### 2.1 الهيكل المقترح

```
app/
└── Http/
    └── Controllers/
        ├── Public/
        │   ├── ItemController.php
        │   ├── RequestController.php
        │   └── SearchController.php
        │
        └── User/
            ├── UserItemController.php
            └── UserRequestController.php
```

### 2.2 مسؤولية كل Folder

#### 2.2.1 `app/Http/Controllers/Public/`

**المسؤولية:**
- Controllers للوصول العام (Public Access)
- متاحة للـ Guests والمستخدمين المسجلين
- لا تتطلب Authentication (إلا في حالات محددة)

**المحتوى:**
- `ItemController.php` - Browse, View Items
- `RequestController.php` - Browse, View Requests
- `SearchController.php` - Search Items, Requests

---

#### 2.2.2 `app/Http/Controllers/User/`

**المسؤولية:**
- Controllers للسياق الخاص بالمستخدم (User Context)
- تتطلب Authentication
- **Read-Only** فقط (لا تعديلات)

**المحتوى:**
- `UserItemController.php` - عرض Items الخاصة بالمستخدم (read-only)
- `UserRequestController.php` - عرض Requests الخاصة بالمستخدم (read-only)

**⚠️ ملاحظة:** هذه Controllers **لا تحتوي** على Create/Update/Delete. هذه العمليات تتم عبر Write Layer (Actions) في سياق مختلف.

---

### 2.3 الفصل عن Write Layer

**Write Layer (موجود):**
```
app/
├── Actions/          # Write Actions
├── Services/        # Write Services
└── Policies/        # Authorization
```

**Read Layer (موجود):**
```
app/
└── Read/            # Read-Only Layer (Query Objects + Read Models)
```

**Controllers Layer (جديد):**
```
app/
└── Http/
    └── Controllers/
        ├── Public/   # Public Controllers
        └── User/     # User Context Controllers (Read-Only)
```

**⚠️ قاعدة:** Controllers **لا تلمس** Write Layer. Controllers **تستخدم فقط** Read Layer.

---

## 3. Controllers List - قائمة Controllers

### 3.1 Public Controllers

#### 3.1.1 ItemController

**Class:** `App\Http\Controllers\Public\ItemController`

**Namespace:** `App\Http\Controllers\Public`

**Purpose:** عرض Items للعامة (Browse, View)

**Methods:**
- `index()` - Browse Items
- `show()` - View Item Details

**Access:** Public (Guests + Authenticated Users)

---

#### 3.1.2 RequestController

**Class:** `App\Http\Controllers\Public\RequestController`

**Namespace:** `App\Http\Controllers\Public`

**Purpose:** عرض Requests للعامة (Browse, View)

**Methods:**
- `index()` - Browse Requests
- `show()` - View Request Details

**Access:** Public (Guests + Authenticated Users)

---

#### 3.1.3 SearchController

**Class:** `App\Http\Controllers\Public\SearchController`

**Namespace:** `App\Http\Controllers\Public`

**Purpose:** البحث في Items و Requests

**Methods:**
- `items()` - Search Items
- `requests()` - Search Requests

**Access:** Public (Guests + Authenticated Users)

---

### 3.2 User Context Controllers

#### 3.2.1 UserItemController

**Class:** `App\Http\Controllers\User\UserItemController`

**Namespace:** `App\Http\Controllers\User`

**Purpose:** عرض Items الخاصة بالمستخدم (Read-Only)

**Methods:**
- `index()` - List User's Items (Read-Only)

**Access:** Authenticated Users Only

**⚠️ ملاحظة:** هذا Controller **لا يحتوي** على Create/Update/Delete. هذه العمليات تتم عبر Write Layer في سياق مختلف (مثل Filament Admin Panel).

---

#### 3.2.2 UserRequestController

**Class:** `App\Http\Controllers\User\UserRequestController`

**Namespace:** `App\Http\Controllers\User`

**Purpose:** عرض Requests الخاصة بالمستخدم (Read-Only)

**Methods:**
- `index()` - List User's Requests (Read-Only)
- `offers()` - View Offers for User's Request

**Access:** Authenticated Users Only

**⚠️ ملاحظة:** هذا Controller **لا يحتوي** على Create/Update/Delete. هذه العمليات تتم عبر Write Layer في سياق مختلف.

---

## 4. Method Signatures - توقيعات Methods

### 4.1 ItemController Methods

#### 4.1.1 index()

**Method:** `index(Request $request): View`

**Purpose:** عرض قائمة Items المعتمدة والمتاحة

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Query Parameters:**
- `operation_type` - 'sell' | 'rent' | 'donate' | null
- `category_id` - int | null
- `price_min` - float | null
- `price_max` - float | null
- `sort` - 'created_at_desc' | 'price_asc' | 'price_desc' | 'title_asc' | 'title_desc' | 'updated_at_desc'
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function index(Request $request): View
{
    // Extract parameters
    $filters = [
        'operation_type' => $request->get('operation_type'),
        'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
        'price_min' => $request->get('price_min') ? (float) $request->get('price_min') : null,
        'price_max' => $request->get('price_max') ? (float) $request->get('price_max') : null,
    ];
    
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object
    $items = app(BrowseItemsQuery::class)->execute($filters, $sort, $page, $perPage);
    
    // Return view (empty state handled in view)
    return view('public.items.index', [
        'items' => $items, // LengthAwarePaginator<ItemReadModel>
        'filters' => $filters,
        'sort' => $sort,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<ItemReadModel>`

**Edge Cases:**
- ✅ Empty results → View shows "No items found"
- ✅ Invalid filters → Ignored by Query Object
- ✅ Invalid page → Query Object returns page 1

---

#### 4.1.2 show()

**Method:** `show(Request $request, int $id, ?string $slug = null): View|RedirectResponse`

**Purpose:** عرض تفاصيل Item واحد

**Input:**
- `Request $request` - HTTP Request
- `int $id` - Item ID (route parameter)
- `?string $slug` - Item Slug (route parameter, optional)

**Logic:**
```php
public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
{
    $user = $request->user(); // null for guests
    
    // Call Query Object
    $item = app(ViewItemQuery::class)->execute($id, $slug, $user);
    
    // Handle null (not found or not visible)
    if (!$item) {
        abort(404, 'Item not found or not visible.');
    }
    
    // Handle slug mismatch (301 Redirect)
    if ($slug && $item->slug !== $slug) {
        return redirect()->route('items.show', ['id' => $item->id, 'slug' => $item->slug], 301);
    }
    
    // Return view
    return view('public.items.show', [
        'item' => $item, // ItemReadModel
    ]);
}
```

**Return:** `View` with `ItemReadModel` or `RedirectResponse` (301 if slug mismatch)

**Edge Cases:**
- ❌ Item not found → `abort(404)`
- ❌ Item not approved → `abort(404)` (except if owner)
- ❌ Item approved but unavailable → `abort(404)` (except if owner)
- ⚠️ Slug mismatch → `redirect(301)` to correct URL

---

### 4.2 RequestController Methods

#### 4.2.1 index()

**Method:** `index(Request $request): View`

**Purpose:** عرض قائمة Requests المعتمدة

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Query Parameters:**
- `status` - 'open' | 'fulfilled' | 'closed' | null
- `category_id` - int | null
- `sort` - 'created_at_desc' | 'status_asc' | 'status_desc' | 'title_asc' | 'title_desc' | 'updated_at_desc'
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function index(Request $request): View
{
    // Extract parameters
    $filters = [
        'status' => $request->get('status'),
        'category_id' => $request->get('category_id') ? (int) $request->get('category_id') : null,
    ];
    
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object
    $requests = app(BrowseRequestsQuery::class)->execute($filters, $sort, $page, $perPage);
    
    // Return view
    return view('public.requests.index', [
        'requests' => $requests, // LengthAwarePaginator<RequestReadModel>
        'filters' => $filters,
        'sort' => $sort,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<RequestReadModel>`

**Edge Cases:**
- ✅ Empty results → View shows "No requests found"
- ✅ Invalid status filter → Ignored by Query Object

---

#### 4.2.2 show()

**Method:** `show(Request $request, int $id, ?string $slug = null): View|RedirectResponse`

**Purpose:** عرض تفاصيل Request واحد

**Input:**
- `Request $request` - HTTP Request
- `int $id` - Request ID (route parameter)
- `?string $slug` - Request Slug (route parameter, optional)

**Logic:**
```php
public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
{
    $user = $request->user(); // null for guests
    
    // Call Query Object
    $requestModel = app(ViewRequestQuery::class)->execute($id, $slug, $user);
    
    // Handle null (not found or not visible)
    if (!$requestModel) {
        abort(404, 'Request not found or not visible.');
    }
    
    // Handle slug mismatch (301 Redirect)
    if ($slug && $requestModel->slug !== $slug) {
        return redirect()->route('requests.show', ['id' => $requestModel->id, 'slug' => $requestModel->slug], 301);
    }
    
    // Return view
    return view('public.requests.show', [
        'request' => $requestModel, // RequestReadModel
    ]);
}
```

**Return:** `View` with `RequestReadModel` or `RedirectResponse` (301 if slug mismatch)

**Edge Cases:**
- ❌ Request not found → `abort(404)`
- ❌ Request not approved → `abort(404)` (except if owner)
- ⚠️ Slug mismatch → `redirect(301)` to correct URL
- ⚠️ Offers: Guests see empty array (handled in Read Model)

---

### 4.3 SearchController Methods

#### 4.3.1 items()

**Method:** `items(Request $request): View`

**Purpose:** البحث في Items

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Query Parameters:**
- `q` - string (required, minimum 2 characters)
- `sort` - 'relevance' | 'created_at_desc' | null
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function items(Request $request): View
{
    // Extract parameters
    $query = trim($request->get('q', ''));
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object (handles query < 2 chars internally)
    $items = app(SearchItemsQuery::class)->execute($query, $sort, $page, $perPage);
    
    // Return view (noindex meta tag - handled in view)
    return view('public.search.items', [
        'items' => $items, // LengthAwarePaginator<ItemReadModel>
        'query' => $query,
        'sort' => $sort,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<ItemReadModel>`

**Edge Cases:**
- ✅ Query < 2 characters → Query Object returns empty paginator
- ✅ No results → View shows "No results found"

---

#### 4.3.2 requests()

**Method:** `requests(Request $request): View`

**Purpose:** البحث في Requests

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Query Parameters:**
- `q` - string (required, minimum 2 characters)
- `sort` - 'relevance' | 'created_at_desc' | null
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function requests(Request $request): View
{
    // Extract parameters
    $query = trim($request->get('q', ''));
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object
    $requests = app(SearchRequestsQuery::class)->execute($query, $sort, $page, $perPage);
    
    // Return view (noindex meta tag - handled in view)
    return view('public.search.requests', [
        'requests' => $requests, // LengthAwarePaginator<RequestReadModel>
        'query' => $query,
        'sort' => $sort,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<RequestReadModel>`

**Edge Cases:**
- ✅ Query < 2 characters → Query Object returns empty paginator
- ✅ No results → View shows "No results found"

---

### 4.4 UserItemController Methods

#### 4.4.1 index()

**Method:** `index(Request $request): View`

**Purpose:** عرض Items الخاصة بالمستخدم (Read-Only)

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Authentication:** Required (middleware: `auth`)

**Query Parameters:**
- `status` - 'pending' | 'approved' | 'rejected' | 'archived' | null (filter by approval status)
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function index(Request $request): View
{
    $user = $request->user(); // Always authenticated
    
    // Extract parameters
    $filters = [
        'status' => $request->get('status'),
    ];
    
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object (User-specific query - to be defined in Read Layer)
    // Note: This might require a new Query Object: UserItemsQuery
    // For now, using BrowseItemsQuery with user filter
    $items = app(BrowseItemsQuery::class)->execute($filters, 'created_at_desc', $page, $perPage);
    
    // Return view
    return view('user.items.index', [
        'items' => $items, // LengthAwarePaginator<ItemReadModel>
        'filters' => $filters,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<ItemReadModel>`

**⚠️ ملاحظة:** قد يتطلب Query Object جديد `UserItemsQuery` في Read Layer لاحقاً.

---

### 4.5 UserRequestController Methods

#### 4.5.1 index()

**Method:** `index(Request $request): View`

**Purpose:** عرض Requests الخاصة بالمستخدم (Read-Only)

**Input:**
- `Request $request` - HTTP Request (query parameters)

**Authentication:** Required (middleware: `auth`)

**Query Parameters:**
- `status` - 'open' | 'fulfilled' | 'closed' | null
- `page` - int (default: 1)
- `per_page` - int (default: 20, max: 50)

**Logic:**
```php
public function index(Request $request): View
{
    $user = $request->user(); // Always authenticated
    
    // Extract parameters
    $filters = [
        'status' => $request->get('status'),
    ];
    
    $page = max(1, (int) $request->get('page', 1));
    $perPage = min(50, max(1, (int) $request->get('per_page', 20)));
    
    // Call Query Object (User-specific query - to be defined in Read Layer)
    // Note: This might require a new Query Object: UserRequestsQuery
    $requests = app(BrowseRequestsQuery::class)->execute($filters, 'created_at_desc', $page, $perPage);
    
    // Return view
    return view('user.requests.index', [
        'requests' => $requests, // LengthAwarePaginator<RequestReadModel>
        'filters' => $filters,
    ]);
}
```

**Return:** `View` with `LengthAwarePaginator<RequestReadModel>`

**⚠️ ملاحظة:** قد يتطلب Query Object جديد `UserRequestsQuery` في Read Layer لاحقاً.

---

#### 4.5.2 offers()

**Method:** `offers(Request $request, int $requestId): View`

**Purpose:** عرض العروض على Request الخاص بالمستخدم

**Input:**
- `Request $request` - HTTP Request
- `int $requestId` - Request ID (route parameter)

**Authentication:** Required (middleware: `auth`)

**Logic:**
```php
public function offers(Request $request, int $requestId): View
{
    $user = $request->user(); // Always authenticated
    
    // Call Query Object
    $offers = app(RequestOffersQuery::class)->execute($requestId, $user);
    
    // Handle empty collection (not owner or no offers)
    // Query Object returns empty collection if user is not owner
    // Controller should verify ownership for 403
    if ($offers->isEmpty()) {
        // Verify if request exists and user is owner
        $requestModel = app(ViewRequestQuery::class)->execute($requestId, null, $user);
        if (!$requestModel || $requestModel->user->id !== $user->id) {
            abort(403, 'You do not have permission to view these offers.');
        }
        // If request exists and user is owner but no offers, show empty state
    }
    
    // Return view (noindex meta tag - handled in view)
    return view('user.requests.offers', [
        'offers' => $offers, // Collection<OfferReadModel>
        'requestId' => $requestId,
    ]);
}
```

**Return:** `View` with `Collection<OfferReadModel>`

**Edge Cases:**
- ❌ Request not found → `abort(404)`
- ❌ User not owner → `abort(403)`
- ✅ No offers → View shows "No offers found"

---

## 5. Error Handling - معالجة الأخطاء

### 5.1 Error Strategy (من READ_LAYER_DESIGN.md)

**Query Objects ترجع:**
- `null` → Item/Request غير موجود أو غير مرئي
- `Collection` → Empty collection إذا لا نتائج
- `LengthAwarePaginator` → Empty paginator إذا لا نتائج

**Controllers تتعامل مع:**
- `null` → `abort(404)`
- Empty Collection → View with empty state
- Empty Paginator → View with empty state
- Slug mismatch → `redirect(301)`
- Forbidden access → `abort(403)`

---

### 5.2 Error Handling Rules

#### 5.2.1 404 Not Found

**يتم إرجاع 404 في الحالات التالية:**

1. ✅ Item/Request غير موجود في Database
2. ✅ Item/Request غير Approved (إلا إذا كان Owner)
3. ✅ Item Approved لكن Unavailable (إلا إذا كان Owner)

**Implementation:**
```php
$item = app(ViewItemQuery::class)->execute($id, $slug, $user);
if (!$item) {
    abort(404, 'Item not found or not visible.');
}
```

---

#### 5.2.2 403 Forbidden

**يتم إرجاع 403 في الحالات التالية:**

1. ✅ User يحاول رؤية Offers على Request ليس Owner له
2. ✅ User يحاول رؤية Offer ليس Owner له

**Implementation:**
```php
$offers = app(RequestOffersQuery::class)->execute($requestId, $user);
if ($offers->isEmpty() && $user) {
    $requestModel = app(ViewRequestQuery::class)->execute($requestId, null, $user);
    if (!$requestModel || $requestModel->user->id !== $user->id) {
        abort(403, 'You do not have permission to view these offers.');
    }
}
```

---

#### 5.2.3 301 Redirect (Slug Mismatch)

**يتم إرجاع 301 Redirect في الحالات التالية:**

1. ✅ Slug غير متطابق مع Item/Request Slug

**Implementation:**
```php
$item = app(ViewItemQuery::class)->execute($id, $slug, $user);
if ($slug && $item->slug !== $slug) {
    return redirect()->route('items.show', ['id' => $item->id, 'slug' => $item->slug], 301);
}
```

---

#### 5.2.4 Empty State Handling

**Empty Collection/Paginator:**
- ✅ لا ترمي Exception
- ✅ تعرض View مع empty state message
- ✅ View تتحقق من `$items->isEmpty()` أو `$items->total() === 0`

**Implementation:**
```php
$items = app(BrowseItemsQuery::class)->execute($filters, $sort, $page, $perPage);
// View handles empty state
return view('public.items.index', ['items' => $items]);
```

---

### 5.3 No Try-Catch in Controllers

**⚠️ قاعدة:** Controllers **لا تحتوي** على try-catch blocks.

**السبب:**
- Query Objects **لا ترمي Exceptions** (ترجع null/empty)
- Database errors يتم التعامل معها في Global Exception Handler
- Controllers بسيطة جداً (Adapter فقط)

---

## 6. View Contracts - عقود Views

### 6.1 View Naming Convention

**Pattern:** `{context}.{entity}.{action}`

**Examples:**
- `public.items.index`
- `public.items.show`
- `public.requests.index`
- `public.requests.show`
- `public.search.items`
- `public.search.requests`
- `user.items.index`
- `user.requests.index`
- `user.requests.offers`

---

### 6.2 Public Views

#### 6.2.1 `public.items.index`

**View:** `resources/views/public/items/index.blade.php`

**Variables:**
```php
[
    'items' => LengthAwarePaginator<ItemReadModel>, // Required
    'filters' => array, // Optional (for filter UI)
    'sort' => string, // Optional (for sort UI)
]
```

**Expected Data:**
- `$items` - Paginated collection of ItemReadModel
- `$items->isEmpty()` - Check if empty
- `$items->total()` - Total count
- `$items->currentPage()` - Current page
- `$items->lastPage()` - Last page
- `$items->links()` - Pagination links

**Empty State:**
- View should check `$items->isEmpty()` and show "No items found" message

---

#### 6.2.2 `public.items.show`

**View:** `resources/views/public/items/show.blade.php`

**Variables:**
```php
[
    'item' => ItemReadModel, // Required
]
```

**Expected Data:**
- `$item->id` - Item ID
- `$item->title` - Item title
- `$item->description` - Item description
- `$item->price` - Item price (nullable)
- `$item->getFormattedPrice()` - Formatted price string
- `$item->operationType` - 'sell' | 'rent' | 'donate'
- `$item->operationTypeLabel` - Formatted label
- `$item->availabilityStatus` - 'available' | 'unavailable'
- `$item->availabilityStatusLabel` - Formatted label
- `$item->slug` - Item slug
- `$item->url` - Full URL
- `$item->user` - UserReadModel (nullable)
- `$item->category` - CategoryReadModel (nullable)
- `$item->images` - Collection<ImageReadModel>
- `$item->primaryImage` - ImageReadModel (nullable)
- `$item->attributes` - Collection<AttributeReadModel>
- `$item->canonicalUrl` - Canonical URL
- `$item->metaTags` - Array of meta tags

**SEO:**
- View should include `$item->canonicalUrl` in `<head>`
- View should include `$item->metaTags` in `<head>`

---

#### 6.2.3 `public.requests.index`

**View:** `resources/views/public/requests/index.blade.php`

**Variables:**
```php
[
    'requests' => LengthAwarePaginator<RequestReadModel>, // Required
    'filters' => array, // Optional
    'sort' => string, // Optional
]
```

**Expected Data:**
- `$requests` - Paginated collection of RequestReadModel
- `$requests->isEmpty()` - Check if empty
- `$requests->total()` - Total count
- `$requests->currentPage()` - Current page
- `$requests->lastPage()` - Last page
- `$requests->links()` - Pagination links

**Empty State:**
- View should check `$requests->isEmpty()` and show "No requests found" message

---

#### 6.2.4 `public.requests.show`

**View:** `resources/views/public/requests/show.blade.php`

**Variables:**
```php
[
    'request' => RequestReadModel, // Required
]
```

**Expected Data:**
- `$request->id` - Request ID
- `$request->title` - Request title
- `$request->description` - Request description
- `$request->status` - 'open' | 'fulfilled' | 'closed'
- `$request->statusLabel` - Formatted label
- `$request->slug` - Request slug
- `$request->url` - Full URL
- `$request->user` - UserReadModel (nullable)
- `$request->category` - CategoryReadModel (nullable)
- `$request->attributes` - Collection<AttributeReadModel>
- `$request->offers` - Collection<OfferReadModel> (empty for guests)
- `$request->offersCount` - Count of pending offers
- `$request->canonicalUrl` - Canonical URL
- `$request->metaTags` - Array of meta tags

**SEO:**
- View should include `$request->canonicalUrl` in `<head>`
- View should include `$request->metaTags` in `<head>`

**Offers Visibility:**
- Guests see empty `$request->offers` collection
- Authenticated users see offers if they are Request Owner or Offer Owner

---

#### 6.2.5 `public.search.items`

**View:** `resources/views/public/search/items.blade.php`

**Variables:**
```php
[
    'items' => LengthAwarePaginator<ItemReadModel>, // Required
    'query' => string, // Required (search query)
    'sort' => string, // Optional
]
```

**Expected Data:**
- `$items` - Paginated collection of ItemReadModel
- `$query` - Search query string
- `$items->isEmpty()` - Check if empty

**SEO:**
- View should include `<meta name="robots" content="noindex, follow">` in `<head>`

**Empty State:**
- View should check `$items->isEmpty()` and show "No results found for '{$query}'" message

---

#### 6.2.6 `public.search.requests`

**View:** `resources/views/public/search/requests.blade.php`

**Variables:**
```php
[
    'requests' => LengthAwarePaginator<RequestReadModel>, // Required
    'query' => string, // Required (search query)
    'sort' => string, // Optional
]
```

**Expected Data:**
- `$requests` - Paginated collection of RequestReadModel
- `$query` - Search query string
- `$requests->isEmpty()` - Check if empty

**SEO:**
- View should include `<meta name="robots" content="noindex, follow">` in `<head>`

**Empty State:**
- View should check `$requests->isEmpty()` and show "No results found for '{$query}'" message

---

### 6.3 User Views

#### 6.3.1 `user.items.index`

**View:** `resources/views/user/items/index.blade.php`

**Variables:**
```php
[
    'items' => LengthAwarePaginator<ItemReadModel>, // Required
    'filters' => array, // Optional
]
```

**Expected Data:**
- `$items` - Paginated collection of ItemReadModel (user's items only)
- `$items->isEmpty()` - Check if empty

**Empty State:**
- View should check `$items->isEmpty()` and show "You haven't created any items yet" message

---

#### 6.3.2 `user.requests.index`

**View:** `resources/views/user/requests/index.blade.php`

**Variables:**
```php
[
    'requests' => LengthAwarePaginator<RequestReadModel>, // Required
    'filters' => array, // Optional
]
```

**Expected Data:**
- `$requests` - Paginated collection of RequestReadModel (user's requests only)
- `$requests->isEmpty()` - Check if empty

**Empty State:**
- View should check `$requests->isEmpty()` and show "You haven't created any requests yet" message

---

#### 6.3.3 `user.requests.offers`

**View:** `resources/views/user/requests/offers.blade.php`

**Variables:**
```php
[
    'offers' => Collection<OfferReadModel>, // Required
    'requestId' => int, // Required
]
```

**Expected Data:**
- `$offers` - Collection of OfferReadModel
- `$offers->isEmpty()` - Check if empty
- `$requestId` - Request ID

**SEO:**
- View should include `<meta name="robots" content="noindex, follow">` in `<head>`

**Empty State:**
- View should check `$offers->isEmpty()` and show "No offers found for this request" message

---

## 7. Cache Integration - تكامل التخزين المؤقت

### 7.1 Cache Layer Location

**Cache Logic:**
- **لا** يوضع داخل Query Objects
- **لا** يوضع داخل Read Models
- **يوضع** في Controller أو Middleware

**⚠️ ملاحظة:** في هذا التصميم، Cache يتم تطبيقه في Controller (conceptual only).

---

### 7.2 Cache Implementation (Conceptual)

#### 7.2.1 Browse Items Cache

**Location:** `ItemController::index()`

**Pseudocode:**
```php
public function index(Request $request): View
{
    // Generate cache key
    $filters = [...];
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $filtersHash = md5(serialize($filters));
    $cacheKey = "items:published:page:{$page}:filters:{$filtersHash}:sort:{$sort}";
    
    // Get from cache or query
    $items = Cache::remember($cacheKey, 300, function() use ($filters, $sort, $page) {
        return app(BrowseItemsQuery::class)->execute($filters, $sort, $page, 20);
    });
    
    return view('public.items.index', ['items' => $items]);
}
```

**TTL:** 300 seconds (5 minutes)

---

#### 7.2.2 Item Details Cache

**Location:** `ItemController::show()`

**Pseudocode:**
```php
public function show(Request $request, int $id, ?string $slug = null): View|RedirectResponse
{
    $user = $request->user();
    $cacheKey = "item:{$id}:details";
    
    // Get from cache or query
    $item = Cache::remember($cacheKey, 600, function() use ($id, $slug, $user) {
        return app(ViewItemQuery::class)->execute($id, $slug, $user);
    });
    
    if (!$item) {
        abort(404);
    }
    
    // Handle slug mismatch...
    
    return view('public.items.show', ['item' => $item]);
}
```

**TTL:** 600 seconds (10 minutes)

---

#### 7.2.3 Search Results Cache

**Location:** `SearchController::items()`

**Pseudocode:**
```php
public function items(Request $request): View
{
    $query = trim($request->get('q', ''));
    $sort = $request->get('sort', 'created_at_desc');
    $page = max(1, (int) $request->get('page', 1));
    $queryHash = md5($query);
    $cacheKey = "items:search:query:{$queryHash}:page:{$page}:sort:{$sort}";
    
    // Get from cache or query (shorter TTL for search)
    $items = Cache::remember($cacheKey, 60, function() use ($query, $sort, $page) {
        return app(SearchItemsQuery::class)->execute($query, $sort, $page, 20);
    });
    
    return view('public.search.items', ['items' => $items, 'query' => $query]);
}
```

**TTL:** 60 seconds (1 minute)

---

### 7.3 Cache Invalidation (Conceptual)

**Cache Invalidation يتم عبر Events/Listeners في Write Layer:**

**Events:**
- `ContentApproved` → Invalidate `item:{id}:details` + `items:published:*`
- `ContentRejected` → Invalidate `item:{id}:details` + `items:published:*`
- `ContentArchived` → Invalidate `item:{id}:details` + `items:published:*`
- `ItemAvailabilityChanged` → Invalidate `item:{id}:details` + `items:published:*`

**⚠️ ملاحظة:** Cache Invalidation **لا يتم** في Controllers. يتم في Event Listeners في Write Layer.

---

### 7.4 Cache Keys Summary

| الصفحة | Cache Key Pattern | TTL |
|--------|------------------|-----|
| Browse Items | `items:published:page:{page}:filters:{hash}:sort:{sort}` | 5 minutes |
| Item Details | `item:{id}:details` | 10 minutes |
| Browse Requests | `requests:published:page:{page}:filters:{hash}:sort:{sort}` | 5 minutes |
| Request Details | `request:{id}:details` | 10 minutes |
| Search Items | `items:search:query:{hash}:page:{page}:sort:{sort}` | 1 minute |
| Search Requests | `requests:search:query:{hash}:page:{page}:sort:{sort}` | 1 minute |

---

## 8. Summary - الملخص

### 8.1 القواعد الإلزامية

1. ✅ **Thin Controllers**: Controllers رقيقة جداً (Adapter فقط)
2. ✅ **No Business Logic**: لا Business Logic داخل Controllers
3. ✅ **Query Objects Only**: الاعتماد فقط على Query Objects
4. ✅ **Error Handling**: تطبيق Error Strategy حرفياً (null → 404, empty → view)
5. ✅ **Read-Only**: لا تعديلات، لا كتابة، لا حذف
6. ✅ **No Try-Catch**: لا try-catch blocks في Controllers

### 8.2 المرجع الإلزامي

**هذا التصميم مبني بالكامل على:**
- `READ_LAYER_DESIGN.md` - Query Objects و Error Strategy
- `PUBLIC_READ_FLOW.md` - URL Structure و SEO Rules

**⚠️ أي تنفيذ يجب أن يلتزم بهذا التصميم 100%.**

---

**الإصدار:** 1.0  
**آخر تحديث:** 2026-01-20  
**الحالة:** ✅ Approved for Implementation
