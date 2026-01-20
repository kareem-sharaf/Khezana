# 📖 Public Read Flow Documentation - Khezana Marketplace
## تدفق القراءة العامة - توثيق واجهات المستخدمين

**الإصدار:** 1.0  
**التاريخ:** 2026-01-20  
**الغرض:** تعريف رسمي وواضح لتدفق القراءة العامة (Read-Only) للكيانات القابلة للعرض العام  
**المرجع الإلزامي:** هذا المستند هو المرجع الوحيد لأي Controller / Query / API سيتم بناؤه لاحقاً

---

## 📌 جدول المحتويات

1. [Overview - نظرة عامة](#1-overview)
2. [Visibility Rules - قواعد الرؤية](#2-visibility-rules)
3. [Public States - الحالات العامة](#3-public-states)
4. [Use Cases - حالات الاستخدام](#4-use-cases)
5. [SEO & URL Strategy - استراتيجية SEO والروابط](#5-seo--url-strategy)
6. [Performance & Safety Rules - قواعد الأداء والأمان](#6-performance--safety-rules)

---

## 1. Overview - نظرة عامة

### 1.1 الهدف

**Public Read Flow** هو طبقة قراءة فقط (Read-Only) مخصصة للعرض العام للمستخدمين (Guests والمستخدمين المسجلين) عبر واجهات Web / SEO.

### 1.2 المبادئ الأساسية

- ✅ **Read-Only**: لا تعديلات، لا كتابة، لا حذف
- ✅ **Low JS**: تصميم للإنترنت الضعيف (Server-Side Rendering)
- ✅ **CQRS Light**: فصل واضح بين Read و Write
- ✅ **SEO-Friendly**: URLs نظيفة، Meta tags، Canonical URLs
- ✅ **Performance First**: Eager Loading، Caching، N+1 Prevention

### 1.3 الكيانات القابلة للعرض العام

| الكيان | قابل للعرض | ملاحظات |
|--------|----------|---------|
| **Item** | ✅ نعم | بشرط Approval + Availability |
| **Request** | ✅ نعم | بشرط Approval |
| **Offer** | ⚠️ محدود | فقط للـ Request Owner أو Offer Owner |

### 1.4 الفصل عن Business Logic

**⚠️ قاعدة إلزامية:**
- Public Read Flow **لا يلمس** أي Business Logic موجود
- لا تعديل على Actions / Services / Policies الموجودة
- لا تعديل على Approval Flow
- لا تعديل على Guards الموجودة
- **فقط** Query Builders و Read-Only Controllers

---

## 2. Visibility Rules - قواعد الرؤية

### 2.1 Item Visibility Rules

#### 2.1.1 الشروط الإلزامية للرؤية

**Item يكون مرئياً للعامة إذا وفقط إذا:**

1. ✅ `Approval.status === APPROVED`
2. ✅ `Item.availability_status === AVAILABLE` (أو `is_available === true` كـ fallback)
3. ✅ `Item.deleted_at === NULL` (Soft Delete)
4. ✅ `Item.archived_at === NULL` (Not Archived)

#### 2.1.2 من يمكنه رؤية Item؟

| المستخدم | يمكنه الرؤية | ملاحظات |
|---------|------------|---------|
| **Guest** | ✅ نعم | فقط Items التي تحقق الشروط أعلاه |
| **Authenticated User** | ✅ نعم | نفس شروط Guest + يمكنه رؤية Items الخاصة به حتى لو غير Approved |
| **Admin** | ✅ نعم | يمكنه رؤية جميع Items (حتى Pending/Rejected) |

#### 2.1.3 الاستثناءات

- **Owner Exception**: مالك Item يمكنه رؤية Item الخاص به حتى لو:
  - `Approval.status === PENDING`
  - `Approval.status === REJECTED`
  - `Item.availability_status === UNAVAILABLE`
  
  **⚠️ لكن:** هذا الاستثناء **لا ينطبق** على Public Read Flow. Owner يستخدم Admin Panel (Filament).

#### 2.1.4 Query Scope المطلوب

```php
// Public Read Query (Read-Only)
Item::query()
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) {
        $q->where('availability_status', ItemAvailability::AVAILABLE)
          ->orWhere('is_available', true); // Fallback
    })
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
```

---

### 2.2 Request Visibility Rules

#### 2.2.1 الشروط الإلزامية للرؤية

**Request يكون مرئياً للعامة إذا وفقط إذا:**

1. ✅ `Approval.status === APPROVED`
2. ✅ `Request.deleted_at === NULL` (Soft Delete)
3. ✅ `Request.archived_at === NULL` (Not Archived)

**⚠️ ملاحظة:** `Request.status` (OPEN/CLOSED/FULFILLED) **لا يؤثر** على الرؤية. Request Approved يظهر دائماً بغض النظر عن RequestStatus.

#### 2.2.2 من يمكنه رؤية Request؟

| المستخدم | يمكنه الرؤية | ملاحظات |
|---------|------------|---------|
| **Guest** | ✅ نعم | فقط Requests التي تحقق الشروط أعلاه |
| **Authenticated User** | ✅ نعم | نفس شروط Guest + يمكنه رؤية Requests الخاصة به حتى لو غير Approved |
| **Admin** | ✅ نعم | يمكنه رؤية جميع Requests (حتى Pending/Rejected) |

#### 2.2.3 RequestStatus و الرؤية

| RequestStatus | مرئي للعامة | ملاحظات |
|--------------|------------|---------|
| **OPEN** | ✅ نعم | Request مفتوح ويقبل عروض |
| **FULFILLED** | ✅ نعم | Request تم الوفاء به (Read-Only) |
| **CLOSED** | ✅ نعم | Request مغلق (Read-Only) |

**⚠️ قاعدة:** RequestStatus يتحكم في **قبول العروض** فقط، وليس في **الرؤية**.

#### 2.2.4 Query Scope المطلوب

```php
// Public Read Query (Read-Only)
Request::query()
    ->whereHas('approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->whereNull('deleted_at')
    ->whereNull('archived_at')
```

---

### 2.3 Offer Visibility Rules

#### 2.3.1 الشروط الإلزامية للرؤية

**Offer يكون مرئياً إذا وفقط إذا:**

1. ✅ `Offer.request` موجود و `Request.approval.status === APPROVED`
2. ✅ المستخدم هو **إما**:
   - Owner of Request (صاحب الطلب)
   - Owner of Offer (صاحب العرض)
   - Admin / Super Admin

#### 2.3.2 من يمكنه رؤية Offer؟

| المستخدم | يمكنه الرؤية | ملاحظات |
|---------|------------|---------|
| **Guest** | ❌ لا | Offers غير مرئية للـ Guests |
| **Request Owner** | ✅ نعم | يمكنه رؤية جميع Offers على Request الخاص به |
| **Offer Owner** | ✅ نعم | يمكنه رؤية Offer الخاص به فقط |
| **Admin** | ✅ نعم | يمكنه رؤية جميع Offers |

#### 2.3.3 OfferStatus و الرؤية

| OfferStatus | مرئي | ملاحظات |
|------------|------|---------|
| **PENDING** | ✅ نعم | للـ Request Owner و Offer Owner |
| **ACCEPTED** | ✅ نعم | للـ Request Owner و Offer Owner |
| **REJECTED** | ✅ نعم | للـ Request Owner و Offer Owner |
| **CANCELLED** | ✅ نعم | للـ Request Owner و Offer Owner |

**⚠️ قاعدة:** OfferStatus **لا يؤثر** على الرؤية. الرؤية تتحكم بها Ownership فقط.

#### 2.3.4 Query Scope المطلوب

```php
// Public Read Query (Read-Only) - للـ Request Owner
Offer::query()
    ->where('request_id', $requestId)
    ->whereHas('request.approval', fn($q) => $q->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) use ($user) {
        $q->whereHas('request', fn($r) => $r->where('user_id', $user->id))
          ->orWhere('user_id', $user->id); // Offer Owner
    })
```

---

## 3. Public States - الحالات العامة

### 3.1 الفرق بين Write States و Read States

**Write States (Business Logic):**
- تتحكم في **التعديل** و **الكتابة**
- مثال: `Item.isPending()` → يمنع التعديل
- مثال: `Request.isClosed()` → يمنع قبول عروض جديدة

**Read States (Public Display):**
- تتحكم في **العرض** و **القراءة** فقط
- مثال: `PublishedItem` → Item Approved + Available
- مثال: `OpenRequest` → Request Approved + Status = OPEN

### 3.2 Item Public States

#### 3.2.1 PublishedItem

**التعريف:**
- Item Approved + Available
- الحالة الوحيدة القابلة للعرض العام

**الشروط:**
```php
Approval.status === APPROVED
AND Item.availability_status === AVAILABLE
AND Item.deleted_at === NULL
AND Item.archived_at === NULL
```

**الاستخدام:**
- Browse Items
- View Item Details
- Search Items

**⚠️ ملاحظة:** Item Approved لكن Unavailable **لا يظهر** في Public Read Flow.

---

#### 3.2.2 AvailableItem (Subset of PublishedItem)

**التعريف:**
- PublishedItem + يمكنه استقبال Offers

**الشروط:**
```php
PublishedItem conditions
AND Item.availability_status === AVAILABLE
```

**الاستخدام:**
- عرض Items المتاحة للعروض
- Filter: "Available Only"

---

### 3.3 Request Public States

#### 3.3.1 PublishedRequest

**التعريف:**
- Request Approved
- الحالة الوحيدة القابلة للعرض العام

**الشروط:**
```php
Approval.status === APPROVED
AND Request.deleted_at === NULL
AND Request.archived_at === NULL
```

**الاستخدام:**
- Browse Requests
- View Request Details
- Search Requests

---

#### 3.3.2 OpenRequest (Subset of PublishedRequest)

**التعريف:**
- PublishedRequest + Status = OPEN
- يقبل عروض جديدة

**الشروط:**
```php
PublishedRequest conditions
AND Request.status === OPEN
```

**الاستخدام:**
- عرض Requests المفتوحة فقط
- Filter: "Open Requests Only"
- Create Offer (لكن هذا Write، ليس Read)

---

#### 3.3.3 FulfilledRequest (Subset of PublishedRequest)

**التعريف:**
- PublishedRequest + Status = FULFILLED
- Read-Only (لا يقبل عروض)

**الشروط:**
```php
PublishedRequest conditions
AND Request.status === FULFILLED
```

**الاستخدام:**
- عرض Requests المكتملة (للإلهام/المرجع)
- Filter: "Fulfilled Requests"

---

#### 3.3.4 ClosedRequest (Subset of PublishedRequest)

**التعريف:**
- PublishedRequest + Status = CLOSED
- Read-Only (لا يقبل عروض)

**الشروط:**
```php
PublishedRequest conditions
AND Request.status === CLOSED
```

**الاستخدام:**
- عرض Requests المغلقة
- Filter: "Closed Requests"

---

### 3.4 لماذا نحتاج Public States؟

1. **SEO Optimization**: URLs مختلفة لكل حالة (مثال: `/items/available`, `/requests/open`)
2. **User Experience**: Filters واضحة ومحددة
3. **Performance**: Queries محسّنة لكل حالة
4. **Caching**: Cache keys مختلفة لكل حالة
5. **Analytics**: تتبع دقيق لكل حالة

---

## 4. Use Cases - حالات الاستخدام

### 4.1 Item Use Cases

#### 4.1.1 Browse Items

**الوصف:** عرض قائمة Items المعتمدة والمتاحة

**Preconditions:**
- لا توجد (Public Read)

**Query Filters:**
- ✅ `Approval.status === APPROVED`
- ✅ `Item.availability_status === AVAILABLE`
- ✅ `Item.deleted_at === NULL`
- ✅ `Item.archived_at === NULL`
- ⚠️ Optional: `operation_type` (SELL, RENT, DONATE)
- ⚠️ Optional: `category_id`
- ⚠️ Optional: `price_min`, `price_max` (للـ SELL/RENT فقط)

**Sorting:**
- Default: `created_at DESC` (الأحدث أولاً)
- Options:
  - `price_asc` / `price_desc` (للـ SELL/RENT فقط)
  - `title_asc` / `title_desc`
  - `updated_at DESC`

**Pagination:**
- Default: 20 items per page
- Max: 50 items per page
- Page parameter: `?page=1`

**Eager Loading:**
```php
->with([
    'user:id,name', // فقط ID و Name (لا email/phone)
    'category:id,name,slug',
    'images' => fn($q) => $q->select('id,item_id,path,is_primary')->orderBy('is_primary', 'desc'),
    'approval:id,approvable_type,approvable_id,status' // للتحقق فقط
])
```

**Edge Cases:**
- ✅ Empty results → Return empty array (لا error)
- ✅ Invalid filters → Ignore invalid filters (لا error)
- ✅ Invalid page number → Return page 1

---

#### 4.1.2 View Item Details

**الوصف:** عرض تفاصيل Item واحد

**Preconditions:**
- Item موجود
- Item Approved + Available (أو User هو Owner)

**Query:**
```php
Item::query()
    ->where('id', $itemId)
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
```

**Eager Loading:**
```php
->with([
    'user:id,name,created_at', // لا email/phone
    'category:id,name,slug,description',
    'images' => fn($q) => $q->select('id,item_id,path,is_primary,alt')->orderBy('is_primary', 'desc'),
    'itemAttributes.attribute:id,name,type', // Dynamic attributes
    'approval:id,approvable_type,approvable_id,status,reviewed_at' // للتحقق فقط
])
```

**Edge Cases:**
- ❌ Item غير موجود → 404 Not Found
- ❌ Item غير Approved → 404 Not Found (إلا إذا كان Owner)
- ❌ Item Approved لكن Unavailable → 404 Not Found (إلا إذا كان Owner)

---

#### 4.1.3 Search Items

**الوصف:** البحث في Items

**Preconditions:**
- Query string موجود (minimum 2 characters)

**Query Filters:**
- ✅ `Approval.status === APPROVED`
- ✅ `Item.availability_status === AVAILABLE`
- ✅ `Item.deleted_at === NULL`
- ✅ `Item.archived_at === NULL`
- ✅ Full-text search على:
  - `Item.title` (LIKE %query%)
  - `Item.description` (LIKE %query%)
  - `Category.name` (LIKE %query%)

**Sorting:**
- Default: Relevance (إذا Full-text search متاح)
- Fallback: `created_at DESC`

**Pagination:**
- Same as Browse Items

**Edge Cases:**
- ✅ Query < 2 characters → Return empty results (لا error)
- ✅ No results → Return empty array (لا error)

---

### 4.2 Request Use Cases

#### 4.2.1 Browse Requests

**الوصف:** عرض قائمة Requests المعتمدة

**Preconditions:**
- لا توجد (Public Read)

**Query Filters:**
- ✅ `Approval.status === APPROVED`
- ✅ `Request.deleted_at === NULL`
- ✅ `Request.archived_at === NULL`
- ⚠️ Optional: `status` (OPEN, FULFILLED, CLOSED)
- ⚠️ Optional: `category_id`

**Sorting:**
- Default: `created_at DESC` (الأحدث أولاً)
- Options:
  - `status_asc` / `status_desc`
  - `title_asc` / `title_desc`
  - `updated_at DESC`

**Pagination:**
- Default: 20 requests per page
- Max: 50 requests per page

**Eager Loading:**
```php
->with([
    'user:id,name', // فقط ID و Name
    'category:id,name,slug',
    'approval:id,approvable_type,approvable_id,status',
    'offers_count' => fn($q) => $q->where('status', OfferStatus::PENDING) // Count فقط
])
```

**Edge Cases:**
- ✅ Empty results → Return empty array
- ✅ Invalid status filter → Ignore (لا error)

---

#### 4.2.2 View Request Details

**الوصف:** عرض تفاصيل Request واحد

**Preconditions:**
- Request موجود
- Request Approved (أو User هو Owner)

**Query:**
```php
Request::query()
    ->where('id', $requestId)
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
```

**Eager Loading:**
```php
->with([
    'user:id,name,created_at',
    'category:id,name,slug,description',
    'itemAttributes.attribute:id,name,type',
    'approval:id,approvable_type,approvable_id,status,reviewed_at',
    'offers' => function($q) use ($user) {
        // فقط للـ Request Owner أو Offer Owner
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
        'item:id,title,primaryImage' // إذا كان Offer مرتبط بـ Item
    ]
])
```

**Edge Cases:**
- ❌ Request غير موجود → 404 Not Found
- ❌ Request غير Approved → 404 Not Found (إلا إذا كان Owner)
- ⚠️ Offers: Guests لا يرون Offers (empty array)

---

#### 4.2.3 View Offers for Request

**الوصف:** عرض العروض على Request (محدود)

**Preconditions:**
- Request موجود و Approved
- User هو **إما**:
  - Request Owner
  - Offer Owner (لرؤية Offer الخاص به فقط)

**Query:**
```php
Offer::query()
    ->where('request_id', $requestId)
    ->whereHas('request.approval', fn($a) => $a->where('status', ApprovalStatus::APPROVED))
    ->where(function($q) use ($user) {
        if (!$user) {
            $q->whereRaw('1 = 0'); // لا Offers للـ Guests
        } else {
            // Request Owner يرى جميع Offers
            $q->whereHas('request', fn($r) => $r->where('user_id', $user->id))
              // Offer Owner يرى Offer الخاص به فقط
              ->orWhere('user_id', $user->id);
        }
    })
```

**Eager Loading:**
```php
->with([
    'user:id,name',
    'item:id,title,price,availability_status,primaryImage' => [
        'images' => fn($q) => $q->where('is_primary', true)->select('id,item_id,path')
    ],
    'request:id,title,status'
])
```

**Edge Cases:**
- ❌ Request غير موجود → 404 Not Found
- ❌ User ليس Owner → 403 Forbidden (أو empty array)
- ✅ No offers → Return empty array

---

### 4.3 Search Use Cases

#### 4.3.1 Global Search

**الوصف:** بحث شامل في Items و Requests

**Preconditions:**
- Query string موجود (minimum 2 characters)

**Query:**
- Search في Items (PublishedItem فقط)
- Search في Requests (PublishedRequest فقط)
- Combine results مع Type indicator

**Sorting:**
- Relevance (إذا Full-text search متاح)
- Fallback: `created_at DESC`

**Pagination:**
- Default: 20 results per page
- Mixed: Items + Requests

**Edge Cases:**
- ✅ Query < 2 characters → Return empty results
- ✅ No results → Return empty array

---

## 5. SEO & URL Strategy - استراتيجية SEO والروابط

### 5.1 URL Structure

#### 5.1.1 Items URLs

| الصفحة | URL Pattern | مثال |
|--------|------------|------|
| Browse Items | `/items` | `/items` |
| Browse by Type | `/items/{type}` | `/items/sell`, `/items/rent`, `/items/donate` |
| Browse Available | `/items/available` | `/items/available` |
| Item Details | `/items/{id}/{slug}` | `/items/123/red-winter-jacket` |
| Search Items | `/items/search?q={query}` | `/items/search?q=jacket` |

**⚠️ قاعدة:** Item Details **يجب** أن يحتوي على `{id}` و `{slug}`. إذا `slug` غير متطابق → 301 Redirect إلى URL الصحيح.

---

#### 5.1.2 Requests URLs

| الصفحة | URL Pattern | مثال |
|--------|------------|------|
| Browse Requests | `/requests` | `/requests` |
| Browse by Status | `/requests/{status}` | `/requests/open`, `/requests/fulfilled` |
| Request Details | `/requests/{id}/{slug}` | `/requests/456/looking-for-winter-coat` |
| Search Requests | `/requests/search?q={query}` | `/requests/search?q=coat` |

**⚠️ قاعدة:** Request Details **يجب** أن يحتوي على `{id}` و `{slug}`.

---

#### 5.1.3 Offers URLs

| الصفحة | URL Pattern | مثال |
|--------|------------|------|
| View Offers (Request Owner) | `/requests/{id}/offers` | `/requests/456/offers` |
| View My Offer | `/offers/{id}` | `/offers/789` |

**⚠️ قاعدة:** Offers **غير قابلة للفهرسة** (noindex - انظر أدناه).

---

### 5.2 Slugs

#### 5.2.1 Item Slug

**التعريف:**
- Slug مستخرج من `Item.title`
- Format: `kebab-case`
- Max length: 100 characters
- Unique per Item (مع ID)

**مثال:**
```
Title: "Red Winter Jacket - Size M"
Slug: "red-winter-jacket-size-m"
URL: /items/123/red-winter-jacket-size-m
```

**⚠️ قاعدة:** إذا Item تم تحديث `title`، Slug **لا يتغير** (لـ SEO stability). Slug جديد يتم إنشاؤه فقط عند إنشاء Item جديد.

---

#### 5.2.2 Request Slug

**نفس قواعد Item Slug:**
- مستخرج من `Request.title`
- Format: `kebab-case`
- Max length: 100 characters
- Unique per Request

---

### 5.3 Canonical URLs

#### 5.3.1 Canonical Rules

| الصفحة | Canonical URL | ملاحظات |
|--------|--------------|---------|
| Item Details | `/items/{id}/{slug}` | دائماً |
| Request Details | `/requests/{id}/{slug}` | دائماً |
| Browse Items | `/items` | بدون query parameters |
| Browse Requests | `/requests` | بدون query parameters |
| Search | `{base_url}/items/search?q={query}` | مع query parameter |

**⚠️ قاعدة:** Canonical URL **يجب** أن يكون في `<head>` لكل صفحة.

---

### 5.4 Meta Rules (Index / Noindex)

#### 5.4.1 Indexable Pages

| الصفحة | Index | ملاحظات |
|--------|------|---------|
| Item Details (PublishedItem) | ✅ `index` | فقط PublishedItem |
| Request Details (PublishedRequest) | ✅ `index` | فقط PublishedRequest |
| Browse Items | ✅ `index` | بدون query parameters |
| Browse Requests | ✅ `index` | بدون query parameters |
| Search Results | ❌ `noindex` | Dynamic content |
| Offers Pages | ❌ `noindex` | Private content |

---

#### 5.4.2 Noindex Rules

**الصفحات التالية **يجب** أن تكون `noindex`:**

1. ✅ Search Results (`/items/search?q=...`, `/requests/search?q=...`)
2. ✅ Offers Pages (`/requests/{id}/offers`, `/offers/{id}`)
3. ✅ Filtered Pages مع Query Parameters (`/items?category=5&price_min=100`)
4. ✅ Pagination Pages بعد Page 1 (`/items?page=2`)
5. ✅ Item/Request غير Approved (404)
6. ✅ Item Approved لكن Unavailable (404)

**⚠️ قاعدة:** أي صفحة تحتوي على Query Parameters (عدا Search `q`) → `noindex`.

---

#### 5.4.3 Meta Tags Structure

**Item Details:**
```html
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://example.com/items/{id}/{slug}">
<meta property="og:type" content="product">
<meta property="og:title" content="{Item.title}">
<meta property="og:description" content="{Item.description (truncated 160)}">
<meta property="og:image" content="{Item.primaryImage.path}">
```

**Request Details:**
```html
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://example.com/requests/{id}/{slug}">
<meta property="og:type" content="article">
<meta property="og:title" content="{Request.title}">
<meta property="og:description" content="{Request.description (truncated 160)}">
```

**Search Results:**
```html
<meta name="robots" content="noindex, follow">
```

---

### 5.5 What is NOT Indexable

**❌ الصفحات التالية **يجب** أن تكون `noindex` أو 404:**

1. Items غير Approved
2. Items Approved لكن Unavailable
3. Requests غير Approved
4. Offers (جميعها)
5. User Profiles (إن وجدت)
6. Admin Pages (`/admin/*`)
7. API Endpoints (`/api/*`)

---

## 6. Performance & Safety Rules - قواعد الأداء والأمان

### 6.1 Eager Loading Rules

#### 6.1.1 Required Eager Loading

**Browse Items:**
```php
->with([
    'user:id,name', // فقط ID و Name
    'category:id,name,slug',
    'images' => fn($q) => $q->select('id,item_id,path,is_primary')
                           ->orderBy('is_primary', 'desc')
                           ->limit(1), // Primary image فقط
    'approval:id,approvable_type,approvable_id,status'
])
```

**View Item Details:**
```php
->with([
    'user:id,name,created_at',
    'category:id,name,slug,description',
    'images' => fn($q) => $q->select('id,item_id,path,is_primary,alt')
                           ->orderBy('is_primary', 'desc'),
    'itemAttributes.attribute:id,name,type', // Nested eager loading
    'approval:id,approvable_type,approvable_id,status,reviewed_at'
])
```

**Browse Requests:**
```php
->with([
    'user:id,name',
    'category:id,name,slug',
    'approval:id,approvable_type,approvable_id,status',
    'offers_count' => fn($q) => $q->selectRaw('request_id, COUNT(*) as count')
                                  ->groupBy('request_id')
])
```

---

#### 6.1.2 Forbidden Eager Loading

**❌ لا تحمّل:**

1. `user.email` (Privacy)
2. `user.phone` (Privacy)
3. `user.password` (Security)
4. `approval.reviewed_by` (Admin only)
5. `approval.rejection_reason` (Admin only)
6. Relations غير مستخدمة في View

---

### 6.2 Forbidden Joins

**❌ لا تستخدم Joins في Public Read Flow:**

1. ❌ `join('approvals', ...)` → استخدم `whereHas('approval', ...)`
2. ❌ `join('users', ...)` → استخدم `with('user')`
3. ❌ `join('categories', ...)` → استخدم `with('category')`

**السبب:** Joins تسبب:
- Column conflicts
- Performance issues
- Hard to maintain

**✅ استخدم:** Eager Loading (`with()`) + `whereHas()`.

---

### 6.3 Max Depth of Relations

**القاعدة:** Maximum 2 levels of nested relations.

**✅ مسموح:**
```php
->with([
    'user', // Level 1
    'category', // Level 1
    'itemAttributes.attribute' // Level 2 (nested)
])
```

**❌ ممنوع:**
```php
->with([
    'user.items.images.category' // Level 4 (ممنوع)
])
```

---

### 6.4 N+1 Prevention Strategy

#### 6.4.1 Always Eager Load

**❌ خطأ:**
```php
$items = Item::published()->get();
foreach ($items as $item) {
    echo $item->user->name; // N+1 Query
    echo $item->category->name; // N+1 Query
}
```

**✅ صحيح:**
```php
$items = Item::published()
    ->with(['user:id,name', 'category:id,name'])
    ->get();
foreach ($items as $item) {
    echo $item->user->name; // No query
    echo $item->category->name; // No query
}
```

---

#### 6.4.2 Count Queries

**❌ خطأ:**
```php
$requests = Request::published()->get();
foreach ($requests as $request) {
    echo $request->offers->count(); // N+1 Query
}
```

**✅ صحيح:**
```php
$requests = Request::published()
    ->withCount('offers')
    ->get();
foreach ($requests as $request) {
    echo $request->offers_count; // No query
}
```

---

### 6.5 Query Optimization Rules

#### 6.5.1 Select Specific Columns

**❌ خطأ:**
```php
Item::published()->get(); // Selects all columns
```

**✅ صحيح:**
```php
Item::published()
    ->select('id', 'title', 'description', 'price', 'operation_type', 'availability_status', 'user_id', 'category_id', 'created_at')
    ->get();
```

**⚠️ ملاحظة:** `select()` **يجب** أن يتضمن Foreign Keys (`user_id`, `category_id`) للـ Eager Loading.

---

#### 6.5.2 Index Usage

**✅ استخدم Indexes الموجودة:**
- `items.availability_status` (indexed)
- `requests.status` (indexed)
- `approvals.status` (indexed)
- `approvals.approvable_type, approvable_id` (unique index)

**⚠️ قاعدة:** Queries **يجب** أن تستخدم Indexed columns في `WHERE` clauses.

---

### 6.6 Caching Strategy

#### 6.6.1 Cache Keys

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

---

#### 6.6.2 Cache TTL

| الصفحة | TTL | ملاحظات |
|--------|-----|---------|
| Browse Items | 5 minutes | Frequently updated |
| Item Details | 10 minutes | Less frequently updated |
| Browse Requests | 5 minutes | Frequently updated |
| Request Details | 10 minutes | Less frequently updated |
| Search Results | 1 minute | Very dynamic |

**⚠️ قاعدة:** Cache **يجب** أن يتم invalidate عند:
- Item/Request Approved
- Item/Request Rejected/Archived
- Item availability_status changed

---

### 6.7 Security Rules

#### 6.7.1 Data Exposure

**❌ لا تعرض:**
- User emails
- User phones
- User passwords
- Approval rejection reasons (Admin only)
- Approval reviewed_by (Admin only)
- Internal IDs (استخدم Public IDs إذا متاح)

**✅ اعرض فقط:**
- User name
- User created_at (للـ "Member since")
- Public Item/Request data
- Public Category data

---

#### 6.7.2 Rate Limiting

**⚠️ قاعدة:** Public Read Endpoints **يجب** أن تكون محمية بـ Rate Limiting:

- Browse: 60 requests/minute
- Details: 120 requests/minute
- Search: 30 requests/minute

---

### 6.8 Error Handling

#### 6.8.1 404 Not Found

**يتم إرجاع 404 في الحالات التالية:**
- Item/Request غير موجود
- Item/Request غير Approved (إلا إذا كان Owner)
- Item Approved لكن Unavailable (إلا إذا كان Owner)
- Invalid slug (301 Redirect إلى URL الصحيح)

---

#### 6.8.2 403 Forbidden

**يتم إرجاع 403 في الحالات التالية:**
- User يحاول رؤية Offers على Request ليس Owner له
- User يحاول رؤية Offer ليس Owner له

---

#### 6.8.3 500 Internal Server Error

**يجب تجنب 500 Errors:**
- Validation على Query Parameters
- Try-Catch حول Database Queries
- Fallback values للـ Missing Relations

---

## 7. Summary - الملخص

### 7.1 القواعد الإلزامية

1. ✅ **Read-Only**: لا تعديلات على Business Logic
2. ✅ **Visibility Rules**: Approval + Availability + Soft Delete
3. ✅ **Public States**: PublishedItem, PublishedRequest, OpenRequest, etc.
4. ✅ **SEO-Friendly**: Slugs, Canonical URLs, Meta Tags
5. ✅ **Performance**: Eager Loading, No N+1, Caching
6. ✅ **Security**: No sensitive data exposure

### 7.2 المرجع الإلزامي

**هذا المستند هو المرجع الوحيد لأي تنفيذ لاحق:**
- Controllers
- Query Builders
- API Endpoints
- Frontend Components

**⚠️ أي تنفيذ يجب أن يلتزم بهذا المستند 100%.**

---

**الإصدار:** 1.0  
**آخر تحديث:** 2026-01-20  
**الحالة:** ✅ Approved for Implementation
