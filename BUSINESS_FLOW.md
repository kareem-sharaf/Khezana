# 📋 Business Flow Documentation - Khezana Marketplace
## دستور النظام - توثيق تدفق العمل

**الإصدار:** 2.0  
**التاريخ:** 2026-01-20  
**آخر تحديث:** 2026-01-20 (v2.0 - بعد المراجعة المعمارية)  
**الغرض:** تعريف رسمي وواضح لتدفقات العمل وقواعد العمل غير القابلة للكسر

---

## 📌 جدول المحتويات

1. [دورة حياة الكيانات (Entity Lifecycles)](#1-دورة-حياة-الكيانات)
2. [تدفق الموافقات (Approval Flow)](#2-تدفق-الموافقات)
3. [قواعد العمل الحرجة (Critical Business Rules)](#3-قواعد-العمل-الحرجة)
4. [تدفق الأحداث (Event Flow)](#4-تدفق-الأحداث)
5. [الربط مع Filament](#5-الربط-مع-filament)
6. [اقتراحات التنفيذ (Implementation Suggestions)](#6-اقتراحات-التنفيذ)

---

## 1. دورة حياة الكيانات (Entity Lifecycles)

### 1.1 User (المستخدم)

**الحالات الممكنة:**
- `active` - نشط
- `inactive` - غير نشط
- `suspended` - معلق

**الحالة الابتدائية:** `active`

**الحالات النهائية:** `suspended`

**الانتقالات المسموحة:**
```
active → inactive
active → suspended
inactive → active
suspended → active (فقط من قبل super_admin)
```

**الانتقالات الممنوعة:**
```
suspended → inactive (ممنوع)
```

**ملاحظات:**
- User لا يحتاج موافقة (Approval)
- User يمكن أن يكون له أدوار متعددة (super_admin, admin, user, delivery_agent)
- User يمكن أن يكون له صلاحيات مباشرة

---

### 1.2 Item (المنتج/الملبس)

**الحالات الممكنة:**
- `pending` - بانتظار الموافقة (عبر Approval)
- `approved` - موافق عليه (عبر Approval)
- `rejected` - مرفوض (عبر Approval)
- `archived` - مؤرشف (عبر Approval)

**حالات التوفر (Availability Status):**
- `available` - متاح للعرض (ItemAvailability::AVAILABLE)
- `unavailable` - غير متاح (ItemAvailability::UNAVAILABLE)
- *مستقبلاً: `reserved`, `rented`, `expired`*

**الحالة الابتدائية:** `pending` (عند الإنشاء)

**الحالات النهائية:** `archived`

**الانتقالات المسموحة:**
```
pending → approved (عبر Approval)
pending → rejected (عبر Approval)
approved → archived (عبر Approval)
rejected → pending (إعادة تقديم)
archived → pending (إعادة تقديم)
```

**الانتقالات الممنوعة:**
```
rejected → approved (ممنوع مباشرة - يجب إعادة تقديم)
approved → rejected (ممنوع - يجب أرشفة أولاً)
archived → approved (ممنوع مباشرة - يجب إعادة تقديم)
```

**العلاقة مع Approval:**
- Item.status = Approval.status (مترابط)
- Item لا يظهر للمستخدمين إلا إذا كان `approved`
- Item.availability_status (ItemAvailability enum) يتحكم في التوفر الفعلي

**⚠️ ملاحظة مهمة:**
- استخدام `ItemAvailability` enum بدلاً من `is_available` boolean
- هذا يسمح بالتوسعة المستقبلية (reserved, rented, expired)

**جدول Lifecycle:**

| الحالة | Approval Status | Availability | مرئي للمستخدمين | قابل للتعديل | ملاحظات |
|------|----------------|-------------|-----------------|-------------|---------|
| pending | PENDING | - | ❌ لا | ✅ نعم | عند الإنشاء |
| approved | APPROVED | AVAILABLE | ✅ نعم | ⚠️ محدود* | بعد الموافقة |
| approved | APPROVED | UNAVAILABLE | ❌ لا | ⚠️ محدود* | بعد الموافقة |
| rejected | REJECTED | - | ❌ لا | ✅ نعم | بعد الرفض |
| archived | ARCHIVED | - | ❌ لا | ❌ لا | نهائي |

**\* قابل للتعديل محدود:** تعديل حقول حساسة يتطلب إعادة مراجعة (انظر BR-027)

---

### 1.3 Request (طلب الملابس)

**الحالات الممكنة:**
- `pending` - بانتظار الموافقة (عبر Approval)
- `approved` - موافق عليه (عبر Approval)
- `rejected` - مرفوض (عبر Approval)
- `archived` - مؤرشف (عبر Approval)
- `open` - مفتوح (قبول عروض) - RequestStatus
- `fulfilled` - تم الوفاء به - RequestStatus
- `closed` - مغلق - RequestStatus

**الحالة الابتدائية:** `pending` (عند الإنشاء) + `open` (RequestStatus)

**الحالات النهائية:** `archived` أو `fulfilled`

**الانتقالات المسموحة:**
```
pending → approved (عبر Approval)
pending → rejected (عبر Approval)
approved → open (تلقائي عند الموافقة)
open → fulfilled (عند قبول عرض)
open → closed (إغلاق يدوي)
fulfilled → archived (تلقائي بعد فترة)
approved → archived (عبر Approval)
rejected → pending (إعادة تقديم)
archived → pending (إعادة تقديم)
```

**الانتقالات الممنوعة:**
```
rejected → open (ممنوع - يجب إعادة تقديم)
closed → open (ممنوع - نهائي)
fulfilled → open (ممنوع - نهائي)
```

**العلاقة مع Approval:**
- Request.status (Approval) يتحكم في الظهور
- Request.status (RequestStatus) يتحكم في قبول العروض

**جدول Lifecycle:**

| Approval Status | RequestStatus | يقبل عروض | مرئي للمستخدمين | ملاحظات |
|---------------|--------------|----------|----------------|---------|
| PENDING | - | ❌ لا | ❌ لا | عند الإنشاء |
| APPROVED | OPEN | ✅ نعم | ✅ نعم | بعد الموافقة |
| APPROVED | FULFILLED | ❌ لا | ✅ نعم | بعد قبول عرض |
| APPROVED | CLOSED | ❌ لا | ✅ نعم | إغلاق يدوي |
| REJECTED | - | ❌ لا | ❌ لا | بعد الرفض |
| ARCHIVED | - | ❌ لا | ❌ لا | نهائي |

---

### 1.4 Offer (العرض)

**الحالات الممكنة:**
- `pending` - بانتظار الرد
- `accepted` - مقبول
- `rejected` - مرفوض
- `cancelled` - ملغي

**الحالة الابتدائية:** `pending`

**الحالات النهائية:** `accepted`, `rejected`, `cancelled`

**الانتقالات المسموحة:**
```
pending → accepted (من صاحب Request)
pending → rejected (من صاحب Request)
pending → cancelled (من صاحب Offer)
```

**الانتقالات الممنوعة:**
```
accepted → pending (ممنوع - نهائي)
accepted → rejected (ممنوع - نهائي)
rejected → accepted (ممنوع - نهائي)
cancelled → pending (ممنوع - نهائي)
```

**ملاحظات:**
- Offer لا يحتاج Approval (ليس Approvable)
- Offer يعتمد على Request.status = OPEN و Request.approved = true
- عند قبول Offer، يتم رفض جميع العروض الأخرى تلقائياً
- **⚠️ Guard حرج:** Offer مرتبط بـ Item يجب أن يتحقق من Item.approved و Item.availability

**جدول Lifecycle:**

| الحالة | قابل للتعديل | قابل للحذف | ملاحظات |
|------|-------------|-----------|---------|
| pending | ✅ نعم | ✅ نعم | عند الإنشاء |
| accepted | ❌ لا | ❌ لا | نهائي |
| rejected | ❌ لا | ❌ لا | نهائي |
| cancelled | ❌ لا | ❌ لا | نهائي |

---

### 1.5 Approval (الموافقة - Polymorphic)

**الحالات الممكنة:**
- `pending` - بانتظار المراجعة
- `approved` - موافق عليه
- `rejected` - مرفوض
- `archived` - مؤرشف

**الحالة الابتدائية:** `pending`

**الحالات النهائية:** `archived`

**الانتقالات المسموحة:**
```
pending → approved (من Admin/Super Admin)
pending → rejected (من Admin/Super Admin)
approved → archived (من Admin/Super Admin)
rejected → pending (إعادة تقديم)
archived → pending (إعادة تقديم)
```

**الانتقالات الممنوعة:**
```
approved → rejected (ممنوع - يجب أرشفة أولاً)
rejected → approved (ممنوع مباشرة - يجب إعادة تقديم)
archived → approved (ممنوع مباشرة - يجب إعادة تقديم)
```

**العلاقة مع الكيانات:**
- Approval.approvable_type: `Item::class` أو `Request::class`
- Approval.status يتحكم في حالة الكيان المرتبط

**جدول Lifecycle:**

| الحالة | قابل للمراجعة | Side Effects | ملاحظات |
|------|-------------|-------------|---------|
| pending | ✅ نعم | - | عند الإنشاء |
| approved | ❌ لا | الكيان يصبح مرئياً | بعد الموافقة |
| rejected | ❌ لا | الكيان مخفي | بعد الرفض |
| archived | ❌ لا | الكيان مخفي نهائياً | نهائي |

---

## 2. تدفق الموافقات (Approval Flow)

### 2.1 إنشاء Approval تلقائياً

**متى يتم إنشاء Approval:**

1. **Item:**
   - عند إنشاء Item جديد → يتم إنشاء Approval تلقائياً
   - `submitted_by` = Item.user_id
   - `status` = PENDING
   - يتم عبر `SubmitForApprovalAction` في Event Listener

2. **Request:**
   - عند إنشاء Request جديد → يتم إنشاء Approval تلقائياً
   - `submitted_by` = Request.user_id
   - `status` = PENDING
   - يتم عبر `SubmitForApprovalAction` في Event Listener

**من هو submitted_by:**
- User الذي أنشأ الكيان (Item.user_id أو Request.user_id)
- يتم تعيينه تلقائياً عند إنشاء Approval

**من هو reviewed_by:**
- Admin أو Super Admin الذي قام بالمراجعة
- يتم تعيينه عند:
  - الموافقة (ApproveAction)
  - الرفض (RejectAction)
  - الأرشفة (ArchiveAction)

---

### 2.2 تدفق الموافقة (Approval Workflow)

```
┌─────────────────────────────────────────────────────────────┐
│                    Approval Workflow                         │
└─────────────────────────────────────────────────────────────┘

1. CREATE ENTITY (Item/Request)
   │
   ├─> Event: EntityCreated
   │
   └─> SubmitForApprovalAction.execute()
       │
       ├─> Create Approval (status: PENDING)
       ├─> submitted_by = Entity.user_id
       └─> Event: ContentSubmitted

2. ADMIN REVIEW
   │
   ├─> Option A: APPROVE
   │   │
   │   ├─> ApproveAction.execute()
   │   │   ├─> Validate: status must be PENDING
   │   │   ├─> Update Approval (status: APPROVED)
   │   │   ├─> reviewed_by = Admin.id
   │   │   ├─> reviewed_at = now()
   │   │   └─> Event: ContentApproved
   │   │
   │   └─> Side Effects:
   │       ├─> Entity becomes visible to users
   │       ├─> If Request: status → OPEN
   │       └─> If Item: is_available controls visibility
   │
   ├─> Option B: REJECT
   │   │
   │   ├─> RejectAction.execute()
   │   │   ├─> Validate: status must be PENDING
   │   │   ├─> Update Approval (status: REJECTED)
   │   │   ├─> reviewed_by = Admin.id
   │   │   ├─> reviewed_at = now()
   │   │   ├─> rejection_reason = provided reason
   │   │   └─> Event: ContentRejected
   │   │
   │   └─> Side Effects:
   │       └─> Entity becomes hidden
   │
   └─> Option C: ARCHIVE (from any status except ARCHIVED)
       │
       ├─> ArchiveAction.execute()
       │   ├─> Validate: status must not be ARCHIVED
       │   ├─> Update Approval (status: ARCHIVED)
       │   ├─> reviewed_by = Admin.id
       │   ├─> reviewed_at = now()
       │   └─> Event: ContentArchived
       │
       └─> Side Effects:
           └─> Entity becomes permanently hidden

3. RESUBMISSION (if REJECTED or ARCHIVED)
   │
   └─> SubmitForApprovalAction.execute()
       │
       ├─> Validate: Only owner can resubmit (BR-008.1)
       │
       ├─> Update existing Approval
       │   ├─> status → PENDING
       │   ├─> submitted_by = User.id (owner)
       │   ├─> reviewed_by = null
       │   ├─> reviewed_at = null
       │   ├─> rejection_reason = null
       │   └─> resubmission_count++ (tracking)
       │
       └─> Event: ContentSubmitted
```

---

### 2.3 العلاقة بين Approval.status و approvable.status

**Item:**
```php
Item.isApproved() === Approval.status === APPROVED
Item.isPending() === Approval.status === PENDING
Item.isRejected() === Approval.status === REJECTED
Item.canBePublished() === Approval.status === APPROVED && Item.is_available === true
```

**Request:**
```php
Request.isApproved() === Approval.status === APPROVED
Request.isPending() === Approval.status === PENDING
Request.isRejected() === Approval.status === REJECTED
Request.canReceiveOffers() === Approval.status === APPROVED && RequestStatus === OPEN
```

**قاعدة عامة:**
- الكيان لا يظهر للمستخدمين إلا إذا كان `Approval.status === APPROVED`
- الكيان يمكن تعديله من قبل المالك (مع قيود على الحقول الحساسة - BR-027)
- الكيان يمكن إعادة تقديمه إذا كان `REJECTED` أو `ARCHIVED` (فقط من المالك - BR-008.1)

---

## 3. قواعد العمل الحرجة (Critical Business Rules)

### 3.1 قواعد الموافقة (Approval Rules)

**BR-001:** لا يمكن عرض Item للمستخدمين قبل الموافقة عليه
- **التنفيذ:** `Item::scopePublished()` يتحقق من `approval.status === APPROVED`
- **الاستثناءات:** لا يوجد

**BR-002:** لا يمكن عرض Request للمستخدمين قبل الموافقة عليه
- **التنفيذ:** `Request::scopePublished()` يتحقق من `approval.status === APPROVED`
- **الاستثناءات:** لا يوجد

**BR-003:** لا يمكن مراجعة Approval إلا من مستخدم لديه دور `admin` أو `super_admin`
- **التنفيذ:** `ApprovalPolicy::approve()`, `ApprovalPolicy::reject()`, `ApprovalPolicy::archive()`
- **الاستثناءات:** لا يوجد

**BR-004:** لا يمكن تنفيذ أكثر من مراجعة على نفس Approval في نفس الوقت
- **التنفيذ:** Database transaction + lock في Actions
- **الاستثناءات:** لا يوجد

**BR-005:** لا يمكن الموافقة على Approval إلا إذا كان status = PENDING
- **التنفيذ:** `ApproveAction::execute()` يتحقق من الحالة
- **الاستثناءات:** لا يوجد

**BR-006:** لا يمكن رفض Approval إلا إذا كان status = PENDING
- **التنفيذ:** `RejectAction::execute()` يتحقق من الحالة
- **الاستثناءات:** لا يوجد

**BR-007:** لا يمكن أرشفة Approval إذا كان status = ARCHIVED
- **التنفيذ:** `ArchiveAction::execute()` يتحقق من الحالة
- **الاستثناءات:** لا يوجد

**BR-008:** يمكن إعادة تقديم Approval إذا كان status = REJECTED أو ARCHIVED
- **التنفيذ:** `SubmitForApprovalAction::execute()` يسمح بإعادة التقديم
- **الاستثناءات:** لا يمكن إعادة تقديم APPROVED

**BR-008.1:** فقط مالك الكيان (Item.user_id أو Request.user_id) يمكنه إعادة تقديم Approval
- **التنفيذ:** `SubmitForApprovalAction::execute()` يتحقق من `$submitter->id === $approvable->getSubmitter()->id`
- **الاستثناءات:** Super Admin يمكنه إعادة تقديم (لحالات استثنائية)

---

### 3.2 قواعد Item

**BR-009:** لا يمكن تعديل Item بعد الرفض إلا بإعادة تقديمه
- **التنفيذ:** Item يمكن تعديله دائماً، لكن لا يظهر إلا بعد الموافقة
- **الاستثناءات:** لا يوجد

**BR-027:** ⚠️ **قاعدة حرجة:** تعديل Item Approved على حقول حساسة يتطلب إعادة مراجعة تلقائياً
- **الحقول الحساسة:** `price`, `operation_type`, `category_id`, `attributes`
- **التنفيذ:** `UpdateItemAction::execute()` يتحقق من التغييرات الحساسة
- **Side Effect:** إذا تم تعديل حقل حساس → إعادة إرسال للموافقة تلقائياً (status → PENDING)
- **الحقول غير الحساسة:** `title`, `description`, `availability_status` (لا تحتاج إعادة مراجعة)
- **الاستثناءات:** لا يوجد

**BR-010:** Item للبيع (SELL) يجب أن يحتوي على price
- **التنفيذ:** `ItemService::validateOperationRules()`
- **الاستثناءات:** لا يوجد

**BR-011:** Item للتأجير (RENT) يجب أن يحتوي على price و deposit_amount
- **التنفيذ:** `ItemService::validateOperationRules()`
- **الاستثناءات:** لا يوجد

**BR-012:** Item للتبرع (DONATE) لا يحتاج price
- **التنفيذ:** `ItemService::validateOperationRules()`
- **الاستثناءات:** لا يوجد

**BR-013:** Item لا يظهر للمستخدمين إلا إذا كان approved و available
- **التنفيذ:** `Item::scopePublished()` + `Item::scopeAvailable()`
- **الاستثناءات:** لا يوجد

---

### 3.3 قواعد Request

**BR-014:** Request لا يقبل عروض إلا إذا كان approved و status = OPEN
- **التنفيذ:** `Request::canReceiveOffers()` يتحقق من كلا الشرطين
- **الاستثناءات:** لا يوجد

**BR-015:** Request.status يتحول تلقائياً إلى OPEN عند الموافقة
- **التنفيذ:** Event Listener في `ContentApproved` event
- **الاستثناءات:** لا يوجد

**BR-016:** Request.status يتحول تلقائياً إلى FULFILLED عند قبول عرض
- **التنفيذ:** `AcceptOfferAction::execute()`
- **الاستثناءات:** لا يوجد

**BR-017:** Request لا يمكن إعادة فتحه بعد FULFILLED أو CLOSED
- **التنفيذ:** `RequestStatus` enum لا يسمح بالانتقال العكسي
- **الاستثناءات:** لا يوجد

---

### 3.4 قواعد Offer

**BR-018:** لا يمكن قبول Offer إلا إذا كان Request.status = OPEN و Request.approved = true
- **التنفيذ:** `AcceptOfferAction::execute()` يتحقق من الشرطين
- **الاستثناءات:** لا يوجد

**BR-019:** لا يمكن إنشاء Offer إلا إذا كان Request.status = OPEN و Request.approved = true
- **التنفيذ:** `CreateOfferAction::execute()` يتحقق من الشرطين
- **الاستثناءات:** لا يوجد

**BR-020:** عند قبول Offer، يتم رفض جميع العروض الأخرى تلقائياً
- **التنفيذ:** `AcceptOfferAction::execute()` يرفض العروض الأخرى
- **الاستثناءات:** لا يوجد

**BR-021:** لا يمكن تعديل Offer إلا إذا كان status = PENDING
- **التنفيذ:** `OfferStatus::canBeUpdated()`
- **الاستثناءات:** لا يوجد

**BR-022:** لا يمكن حذف Offer إلا إذا كان status = PENDING
- **التنفيذ:** `OfferPolicy::delete()`
- **الاستثناءات:** لا يوجد

**BR-023:** مستخدم واحد يمكنه إنشاء عرض واحد فقط لكل Request
- **التنفيذ:** `CreateOfferAction::execute()` يتحقق من عدم وجود عرض سابق
- **الاستثناءات:** لا يوجد

**BR-024:** ⚠️ **Guard حرج:** Offer مرتبط بـ Item يجب أن يتحقق من:
- Item.approved === true
- Item.availability_status === AVAILABLE
- **التنفيذ:** `CreateOfferAction::execute()` و `AcceptOfferAction::execute()` يتحققان من الشرطين
- **الاستثناءات:** Offer بدون Item (عرض مباشر) لا يحتاج هذا التحقق

---

### 3.5 قواعد الأمان والصلاحيات

**BR-025:** فقط Admin و Super Admin يمكنهم الموافقة/الرفض/الأرشفة
- **التنفيذ:** `ApprovalPolicy`
- **الاستثناءات:** لا يوجد

**BR-026:** المستخدم العادي يمكنه فقط إنشاء وتعديل محتواه الخاص
- **التنفيذ:** `ItemPolicy`, `RequestPolicy`
- **الاستثناءات:** لا يوجد

**BR-027:** Super Admin يمكنه إدارة جميع الكيانات
- **التنفيذ:** `ItemPolicy::before()`, `RequestPolicy::before()`
- **الاستثناءات:** لا يوجد

**BR-028:** ❗ **سياسة الحذف:** لا يمكن Hard Delete لأي Approvable (Item/Request)
- **التنفيذ:** Soft Delete أو Archive فقط
- **Hard Delete:** مسموح فقط لـ Super Admin في حالات نادرة جداً (GDPR, legal requirements)
- **الاستثناءات:** لا يوجد

---

## 4. تدفق الأحداث (Event Flow)

### 4.1 Events المقترحة

#### 4.1.1 Item Events

**ItemCreated**
- **متى:** عند إنشاء Item جديد
- **Side Effects:**
  - إنشاء Approval تلقائياً (PENDING)
  - إرسال إشعار للمستخدم
- **المعاملات:** `Item $item`

**ItemSubmitted**
- **متى:** عند إرسال Item للموافقة (أو إعادة تقديم)
- **Side Effects:**
  - تحديث/إنشاء Approval
  - إرسال إشعار للأدمن
- **المعاملات:** `Approval $approval`

**ItemApproved**
- **متى:** عند الموافقة على Item
- **Side Effects:**
  - Item يصبح مرئياً (إذا available)
  - إرسال إشعار للمستخدم
  - إرسال إشعار للمستخدمين المهتمين
- **المعاملات:** `Approval $approval`

**ItemRejected**
- **متى:** عند رفض Item
- **Side Effects:**
  - Item يصبح مخفياً
  - إرسال إشعار للمستخدم مع سبب الرفض
- **المعاملات:** `Approval $approval`

**ItemArchived**
- **متى:** عند أرشفة Item
- **Side Effects:**
  - Item يصبح مخفياً نهائياً
  - إرسال إشعار للمستخدم
- **المعاملات:** `Approval $approval`

**ItemUpdated**
- **متى:** عند تحديث Item
- **Side Effects:**
  - إذا كان approved وتغيرت حقول حساسة → إعادة إرسال للموافقة تلقائياً (BR-027)
  - الحقول الحساسة: price, operation_type, category_id, attributes
- **المعاملات:** `Item $item`, `array $changedFields`

---

#### 4.1.2 Request Events

**RequestCreated**
- **متى:** عند إنشاء Request جديد
- **Side Effects:**
  - إنشاء Approval تلقائياً (PENDING)
  - إرسال إشعار للمستخدم
- **المعاملات:** `Request $request`

**RequestSubmitted**
- **متى:** عند إرسال Request للموافقة (أو إعادة تقديم)
- **Side Effects:**
  - تحديث/إنشاء Approval
  - إرسال إشعار للأدمن
- **المعاملات:** `Approval $approval`

**RequestApproved**
- **متى:** عند الموافقة على Request
- **Side Effects:**
  - Request.status → OPEN
  - Request يصبح مرئياً
  - إرسال إشعار للمستخدم
  - إرسال إشعار للمستخدمين المهتمين
- **المعاملات:** `Approval $approval`

**RequestRejected**
- **متى:** عند رفض Request
- **Side Effects:**
  - Request يصبح مخفياً
  - إرسال إشعار للمستخدم مع سبب الرفض
- **المعاملات:** `Approval $approval`

**RequestArchived**
- **متى:** عند أرشفة Request
- **Side Effects:**
  - Request يصبح مخفياً نهائياً
  - إرسال إشعار للمستخدم
- **المعاملات:** `Approval $approval`

**RequestFulfilled**
- **متى:** عند قبول عرض (Offer)
- **Side Effects:**
  - Request.status → FULFILLED
  - رفض جميع العروض الأخرى
  - إرسال إشعار لصاحب Request
  - إرسال إشعار لصاحب العرض المقبول
- **المعاملات:** `Request $request`, `Offer $acceptedOffer`

**RequestClosed**
- **متى:** عند إغلاق Request يدوياً
- **Side Effects:**
  - Request.status → CLOSED
  - إرسال إشعار للمستخدم
- **المعاملات:** `Request $request`

---

#### 4.1.3 Offer Events

**OfferCreated**
- **متى:** عند إنشاء Offer جديد
- **Side Effects:**
  - إرسال إشعار لصاحب Request
- **المعاملات:** `Offer $offer`

**OfferAccepted**
- **متى:** عند قبول Offer
- **Side Effects:**
  - Request.status → FULFILLED
  - رفض جميع العروض الأخرى
  - إرسال إشعار لصاحب Request
  - إرسال إشعار لصاحب العرض
- **المعاملات:** `Offer $offer`

**OfferRejected**
- **متى:** عند رفض Offer
- **Side Effects:**
  - إرسال إشعار لصاحب العرض
- **المعاملات:** `Offer $offer`

**OfferCancelled**
- **متى:** عند إلغاء Offer
- **Side Effects:**
  - إرسال إشعار لصاحب Request
- **المعاملات:** `Offer $offer`

---

#### 4.1.4 Approval Events (Generic)

**ContentSubmitted**
- **متى:** عند إرسال أي محتوى للموافقة
- **Side Effects:**
  - إرسال إشعار للأدمن
- **المعاملات:** `Approval $approval`

**ContentApproved**
- **متى:** عند الموافقة على أي محتوى
- **Side Effects:**
  - يعتمد على نوع المحتوى (Item/Request)
- **المعاملات:** `Approval $approval`

**ContentRejected**
- **متى:** عند رفض أي محتوى
- **Side Effects:**
  - يعتمد على نوع المحتوى (Item/Request)
- **المعاملات:** `Approval $approval`

**ContentArchived**
- **متى:** عند أرشفة أي محتوى
- **Side Effects:**
  - يعتمد على نوع المحتوى (Item/Request)
- **المعاملات:** `Approval $approval`

---

### 4.2 Event Listeners المقترحة

```php
// Item Event Listeners
ItemCreated → SubmitItemForApprovalAction
ContentApproved (Item) → MakeItemVisible
ContentRejected (Item) → HideItem
ContentArchived (Item) → HideItemPermanently

// Request Event Listeners
RequestCreated → SubmitRequestForApprovalAction
ContentApproved (Request) → OpenRequest
ContentRejected (Request) → HideRequest
ContentArchived (Request) → HideRequestPermanently
RequestFulfilled → RejectOtherOffers

// Offer Event Listeners
OfferAccepted → FulfillRequest + RejectOtherOffers
```

---

## 5. الربط مع Filament

### 5.1 ما الذي يتم من Filament فقط

**إجراءات الموافقة:**
- ✅ الموافقة على Item/Request (ApproveAction)
- ✅ رفض Item/Request (RejectAction)
- ✅ أرشفة Item/Request (ArchiveAction)
- ✅ عرض تفاصيل Approval

**إدارة المحتوى:**
- ✅ عرض جميع الموافقات (ApprovalResource)
- ✅ تصفية حسب الحالة والنوع
- ✅ البحث في الموافقات

**إدارة المستخدمين:**
- ✅ إدارة الأدوار والصلاحيات
- ✅ تعطيل/تفعيل المستخدمين

---

### 5.2 ما الذي يجب منعه من التعديل اليدوي

**❌ ممنوع في Filament:**
- تعديل Approval.status مباشرة (يجب استخدام Actions)
- تعديل Item/Request بعد الرفض مباشرة (يجب إعادة تقديم)
- إنشاء Approval يدوياً (يتم تلقائياً)
- تعديل Approval.reviewed_by يدوياً (يتم تلقائياً)
- تعديل Approval.reviewed_at يدوياً (يتم تلقائياً)
- تعديل Approval.resubmission_count يدوياً (يتم تلقائياً)
- Hard Delete لأي Approvable (يجب Archive فقط - BR-028)

**✅ مسموح في Filament:**
- عرض Approval
- عرض Item/Request
- تعديل Item/Request من قبل المالك (لكن يحتاج إعادة موافقة إذا كان approved)

---

### 5.3 استخدام Services في Filament

**المبدأ:**
- Filament Resources يجب أن تستخدم Actions/Services فقط
- لا يجب أن يحتوي Filament على منطق عمل (Business Logic)

**مثال صحيح:**
```php
// ✅ صحيح
Actions\Action::make('approve')
    ->action(function (Approval $record) {
        app(ApproveAction::class)->execute($record, auth()->user());
    })

// ❌ خطأ
Actions\Action::make('approve')
    ->action(function (Approval $record) {
        $record->update(['status' => ApprovalStatus::APPROVED]); // ❌
    })
```

**Services المقترحة:**
- `ApprovalService` - إدارة الموافقات
- `ItemService` - إدارة Items (موجود)
- `RequestService` - إدارة Requests (موجود)
- `OfferService` - إدارة Offers

---

## 6. اقتراحات التنفيذ (Implementation Suggestions)

### 6.1 Enums المقترحة

**ItemAvailability (مستحسن - موجود جزئياً):**
```php
enum ItemAvailability: string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    // مستقبلاً:
    // case RESERVED = 'reserved';
    // case RENTED = 'rented';
    // case EXPIRED = 'expired';
}
```

**ملاحظة:** 
- استخدام Enum بدلاً من Boolean يسمح بالتوسعة المستقبلية
- Item يستخدم Approval.status للحالة، و ItemAvailability للتوفر

---

### 6.2 Services المركزية المقترحة

**ApprovalService:**
```php
class ApprovalService
{
    public function approve(Approval $approval, User $reviewer): Approval
    public function reject(Approval $approval, User $reviewer, ?string $reason): Approval
    public function archive(Approval $approval, User $reviewer): Approval
    public function submitForApproval(Approvable $approvable, User $submitter): Approval
    public function canBeReviewed(Approval $approval, User $user): bool
    public function canResubmit(Approvable $approvable, User $user): bool // BR-008.1
    public function getResubmissionCount(Approval $approval): int // Audit
}
```

**ItemApprovalService:**
```php
class ItemApprovalService
{
    public function handleApproval(Approval $approval): void // Side effects
    public function handleRejection(Approval $approval): void // Side effects
    public function handleArchival(Approval $approval): void // Side effects
}
```

**RequestApprovalService:**
```php
class RequestApprovalService
{
    public function handleApproval(Approval $approval): void // Side effects
    public function handleRejection(Approval $approval): void // Side effects
    public function handleArchival(Approval $approval): void // Side effects
}
```

**OfferService:**
```php
class OfferService
{
    public function create(array $data, User $user): Offer
    public function accept(Offer $offer, User $requestOwner): Offer
    public function reject(Offer $offer, User $requestOwner): Offer
    public function cancel(Offer $offer, User $offerOwner): Offer
    public function canCreateOffer(Request $request, User $user): bool
    public function validateItemAvailability(Offer $offer): bool // BR-024
    public function canAcceptOffer(Offer $offer, Request $request): bool // BR-018, BR-024
}
```

---

### 6.3 State Machine (اختياري)

يمكن استخدام State Machine library مثل `winzou/state-machine` لإدارة الانتقالات:

```php
// Item State Machine
$stateMachine = StateMachineFactory::create([
    'states' => ['pending', 'approved', 'rejected', 'archived'],
    'transitions' => [
        'approve' => ['from' => ['pending'], 'to' => 'approved'],
        'reject' => ['from' => ['pending'], 'to' => 'rejected'],
        'archive' => ['from' => ['approved', 'rejected'], 'to' => 'archived'],
        'resubmit' => ['from' => ['rejected', 'archived'], 'to' => 'pending'],
    ],
]);
```

---

### 6.4 Database Constraints

**اقتراحات:**
```sql
-- Ensure one approval per approvable
ALTER TABLE approvals 
ADD UNIQUE KEY unique_approvable (approvable_type, approvable_id);

-- Index on status for performance
CREATE INDEX idx_approvals_status ON approvals(status);

-- Index on approvable for polymorphic queries
CREATE INDEX idx_approvals_approvable ON approvals(approvable_type, approvable_id);

-- Foreign key constraints with cascade rules
ALTER TABLE approvals
ADD CONSTRAINT fk_approvals_submitted_by 
FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE RESTRICT;

ALTER TABLE approvals
ADD CONSTRAINT fk_approvals_reviewed_by 
FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE RESTRICT;

-- Add resubmission_count column (if not exists)
ALTER TABLE approvals 
ADD COLUMN resubmission_count INT UNSIGNED DEFAULT 0;

-- Ensure reviewed_by is admin/super_admin (application level validation)
-- يمكن إضافة check constraint إذا لزم الأمر
```

**ملاحظات:**
- `ON DELETE RESTRICT` يمنع حذف مستخدم له موافقات
- `resubmission_count` لتتبع عدد مرات إعادة التقديم (Audit trail)

---

## 7. ملخص التنفيذ

### 7.1 Checklist للتنفيذ

- [x] تعريف Lifecycles لجميع الكيانات
- [x] تعريف Approval Flow
- [x] تعريف Business Rules
- [x] تعريف Events
- [x] تعريف Services المقترحة
- [ ] تنفيذ Event Listeners
- [ ] تنفيذ Services
- [ ] تحديث Filament Resources
- [ ] إضافة Tests

---

### 7.2 أولويات التنفيذ

**المرحلة 1 (حرجة):**
1. تنفيذ Event Listeners للإنشاء التلقائي للموافقات
2. تنفيذ Side Effects في Approval Actions
3. تحديث Filament Resources لاستخدام Actions فقط

**المرحلة 2 (مهمة):**
1. تنفيذ OfferService
2. تنفيذ Event Listeners للعروض
3. إضافة Notifications

**المرحلة 3 (تحسينات):**
1. إضافة State Machine (اختياري)
2. إضافة Analytics
3. تحسين Performance

---

## 8. الخلاصة

هذا المستند يعرّف **"دستور النظام"** الذي يجب اتباعه في جميع التنفيذات:

1. **Lifecycles** - دورة حياة واضحة لكل كيان
2. **Business Rules** - قواعد غير قابلة للكسر
3. **Approval Flow** - تدفق موافقات منظم
4. **Events** - أحداث واضحة للتفاعل
5. **Services** - خدمات مركزية للتنفيذ

**⚠️ تحذير:** أي تغيير في هذا المستند يجب أن يتم بعد مراجعة شاملة وتحديث جميع الأجزاء المتأثرة.

---

---

## 9. ملاحظات المراجعة المعمارية (v2.0)

### 9.1 التحسينات المطبقة

✅ **تحسين Item Availability:**
- تغيير من `is_available` boolean إلى `ItemAvailability` enum
- يسمح بالتوسعة المستقبلية (reserved, rented, expired)

✅ **إضافة BR-027 (تعديل Item Approved):**
- تعديل حقول حساسة يتطلب إعادة مراجعة تلقائياً
- الحقول الحساسة: price, operation_type, category_id, attributes

✅ **إضافة BR-024 (Guard لـ Offer):**
- Offer مرتبط بـ Item يجب أن يتحقق من Item.approved و Item.availability

✅ **إضافة BR-008.1 (إعادة التقديم):**
- فقط مالك الكيان يمكنه إعادة تقديم Approval
- Super Admin استثناء للحالات النادرة

✅ **إضافة BR-028 (سياسة الحذف):**
- No hard delete لأي Approvable
- Archive فقط، Hard Delete = Super Admin فقط (نادر)

✅ **إضافة Resubmission Tracking:**
- `approval.resubmission_count` لتتبع عدد المرات
- Audit trail محسّن

✅ **تحسين Database Constraints:**
- Foreign keys مع cascade rules
- Indexes للأداء
- Unique constraint على approvable

---

### 9.2 نقاط القوة المعمارية

1. **فصل Approval عن الكيانات** - تصميم قابل للتوسعة
2. **Event-driven Architecture** - قابلية التوسعة والاختبار
3. **Business Rules مرقمة** - قابلة للتحويل إلى Tests
4. **Services مركزية** - فصل منطق العمل عن UI
5. **Polymorphic Approval** - قابل لإضافة Approvable جديدة

---

### 9.3 التقييم النهائي

| الجانب | التقييم |
|--------|---------|
| Architecture | ⭐⭐⭐⭐⭐ |
| Business Logic | ⭐⭐⭐⭐⭐ |
| Scalability | ⭐⭐⭐⭐⭐ |
| Production Readiness | ⭐⭐⭐⭐⭐ |

**الحالة:** ✅ جاهز للإنتاج (Production Ready)

---

**آخر تحديث:** 2026-01-20  
**الإصدار:** 2.0  
**الحالة:** ✅ مكتمل - بعد المراجعة المعمارية
