# 📁 ملخص هيكل مجلد App - مشروع خزانة

## 🏗️ الهيكل العام

```
app/
├── Actions/              # إجراءات العمل (Action Classes)
├── Contracts/            # العقود والواجهات (Interfaces)
├── DTOs/                 # كائنات نقل البيانات (Data Transfer Objects)
├── Enums/                # التعدادات (Enumerations)
├── Events/               # الأحداث (Events)
├── Filament/             # لوحة تحكم Filament
├── Helpers/              # الدوال المساعدة (Helper Functions)
├── Http/                 # طبقة HTTP
│   ├── Controllers/      # المتحكمات (Controllers)
│   ├── Middleware/       # البرمجيات الوسطية (Middleware)
│   └── Requests/         # طلبات التحقق (Form Requests)
├── Listeners/            # مستمعي الأحداث (Event Listeners)
├── Models/               # النماذج (Models)
├── Policies/             # السياسات (Authorization Policies)
├── Providers/            # مقدمي الخدمات (Service Providers)
├── Read/                 # نماذج القراءة (Read Models - CQRS)
├── Repositories/         # المستودعات (Repositories)
├── Services/             # الخدمات (Business Logic Services)
├── Traits/               # السمات (Traits)
├── View/                 # مكونات العرض (View Components)
└── ViewModels/           # نماذج العرض (View Models)
```

---

## 📂 تفاصيل المجلدات والملفات

### 1️⃣ Actions/ - إجراءات العمل

**الغرض**: فصل منطق العمل المعقد إلى إجراءات منفصلة قابلة لإعادة الاستخدام

#### Actions/Approval/
- **ApproveContentAction.php**: إجراء الموافقة على المحتوى (Items/Requests)
- **RejectContentAction.php**: إجراء رفض المحتوى
- **ArchiveContentAction.php**: إجراء أرشفة المحتوى
- **SubmitContentForApprovalAction.php**: إجراء إرسال المحتوى للموافقة

#### Actions/Attribute/
- **AssignAttributeToCategoryAction.php**: إجراء ربط خاصية بفئة معينة
- **RemoveAttributeFromCategoryAction.php**: إجراء إزالة خاصية من فئة

#### Actions/Category/
- **CreateCategoryAction.php**: إجراء إنشاء فئة جديدة
- **UpdateCategoryAction.php**: إجراء تحديث فئة موجودة
- **DeleteCategoryAction.php**: إجراء حذف فئة

#### Actions/Item/
- **CreateItemAction.php**: إجراء إنشاء عنصر جديد
- **UpdateItemAction.php**: إجراء تحديث عنصر موجود
- **DeleteItemAction.php**: إجراء حذف عنصر
- **ApproveItemAction.php**: إجراء الموافقة على عنصر
- **RejectItemAction.php**: إجراء رفض عنصر
- **ArchiveItemAction.php**: إجراء أرشفة عنصر
- **SubmitItemForApprovalAction.php**: إجراء إرسال عنصر للموافقة

#### Actions/Offer/
- **CreateOfferAction.php**: إجراء إنشاء عرض جديد
- **UpdateOfferAction.php**: إجراء تحديث عرض موجود
- **AcceptOfferAction.php**: إجراء قبول عرض
- **RejectOfferAction.php**: إجراء رفض عرض
- **CancelOfferAction.php**: إجراء إلغاء عرض

#### Actions/Request/
- **CreateRequestAction.php**: إجراء إنشاء طلب جديد
- **UpdateRequestAction.php**: إجراء تحديث طلب موجود
- **DeleteRequestAction.php**: إجراء حذف طلب
- **ApproveRequestAction.php**: إجراء الموافقة على طلب
- **RejectRequestAction.php**: إجراء رفض طلب
- **ArchiveRequestAction.php**: إجراء أرشفة طلب
- **CloseRequestAction.php**: إجراء إغلاق طلب
- **SubmitRequestForApprovalAction.php**: إجراء إرسال طلب للموافقة

---

### 2️⃣ Contracts/ - العقود والواجهات

**الغرض**: تعريف واجهات برمجية لضمان التناسق في التنفيذ

- **Approvable.php**: واجهة للنماذج التي يمكن الموافقة عليها (Items, Requests). تحدد الطرق المطلوبة مثل `isApproved()`, `isPending()`, `canBePublished()`

---

### 3️⃣ DTOs/ - كائنات نقل البيانات

**الغرض**: نقل البيانات بين الطبقات بشكل منظم وآمن

- **UserDTO.php**: كائن نقل بيانات المستخدم
- **UserProfileDTO.php**: كائن نقل بيانات ملف المستخدم الشخصي
- **AdminActionLogDTO.php**: كائن نقل بيانات سجل إجراءات المدير

---

### 4️⃣ Enums/ - التعدادات

**الغرض**: تعريف القيم الثابتة المنظمة

- **ApprovalStatus.php**: حالات الموافقة (pending, approved, rejected, archived)
- **AttributeType.php**: أنواع الخصائص (size, color, condition, fabric, etc.)
- **ItemAvailability.php**: حالة توفر العنصر (available, sold, rented, etc.)
- **OfferStatus.php**: حالات العروض (pending, accepted, rejected, cancelled)
- **OperationType.php**: أنواع العمليات (sell, rent, donate)
- **RequestStatus.php**: حالات الطلبات (open, closed, fulfilled)

---

### 5️⃣ Events/ - الأحداث

**الغرض**: إرسال إشعارات عند حدوث أحداث مهمة في النظام

#### Events/Approval/
- **ContentApproved.php**: حدث عند الموافقة على محتوى
- **ContentRejected.php**: حدث عند رفض محتوى
- **ContentArchived.php**: حدث عند أرشفة محتوى
- **ContentSubmitted.php**: حدث عند إرسال محتوى للموافقة

#### Events/
- **UserCreated.php**: حدث عند إنشاء مستخدم جديد
- **UserUpdated.php**: حدث عند تحديث مستخدم
- **UserDeleted.php**: حدث عند حذف مستخدم

---

### 6️⃣ Filament/ - لوحة تحكم Filament

**الغرض**: إدارة لوحة التحكم الإدارية باستخدام Filament

#### Filament/Pages/
- **PlatformSettings.php**: صفحة إعدادات المنصة

#### Filament/Resources/
كل مورد يحتوي على صفحات CRUD كاملة:

- **ApprovalResource.php**: إدارة الموافقات
  - ListApprovals.php, ViewApproval.php

- **AttributeResource.php**: إدارة الخصائص
  - CreateAttribute.php, EditAttribute.php, ListAttributes.php, ViewAttribute.php

- **CategoryResource.php**: إدارة الفئات
  - CreateCategory.php, EditCategory.php, ListCategories.php, ViewCategory.php

- **ItemResource.php**: إدارة العناصر
  - ListItems.php, ViewItem.php

- **OfferResource.php**: إدارة العروض
  - ListOffers.php, ViewOffer.php

- **PermissionResource.php**: إدارة الصلاحيات
  - CreatePermission.php, EditPermission.php, ListPermissions.php, ViewPermission.php

- **RequestResource.php**: إدارة الطلبات
  - ListRequests.php, ViewRequest.php

- **RoleResource.php**: إدارة الأدوار
  - CreateRole.php, EditRole.php, ListRoles.php, ViewRole.php

- **UserResource.php**: إدارة المستخدمين
  - CreateUser.php, EditUser.php, ListUsers.php, ViewUser.php

---

### 7️⃣ Helpers/ - الدوال المساعدة

**الغرض**: دوال مساعدة عامة تستخدم في جميع أنحاء التطبيق

- **helpers.php**: الدوال المساعدة العامة
  - `setting()`: الحصول على قيمة إعداد
  - `price_with_fee()`: حساب السعر مع رسوم التوصيل
  - `seo()`: توليد علامات SEO
  - `translate_attribute_name()`: ترجمة اسم الخاصية

- **ItemCardHelper.php**: دوال مساعدة لعرض بطاقات العناصر
- **TranslationHelper.php**: دوال مساعدة للترجمة

---

### 8️⃣ Http/ - طبقة HTTP

#### Http/Controllers/ - المتحكمات

**الغرض**: معالجة طلبات HTTP وإرجاع الاستجابات

##### Controllers/Auth/
- **AuthenticatedSessionController.php**: تسجيل الدخول والخروج
- **ConfirmablePasswordController.php**: تأكيد كلمة المرور
- **EmailVerificationPromptController.php**: عرض نافذة التحقق من البريد
- **NewPasswordController.php**: إعادة تعيين كلمة المرور
- **PasswordController.php**: تحديث كلمة المرور
- **ProfileController.php**: إدارة الملف الشخصي
- **RegisteredUserController.php**: تسجيل مستخدم جديد
- **VerifyEmailController.php**: التحقق من البريد الإلكتروني

##### Controllers/Public/
- **ItemController.php**: عرض العناصر للجمهور (قائمة، تفاصيل)
- **RequestController.php**: عرض الطلبات للجمهور (قائمة، تفاصيل)

##### Controllers/
- **Controller.php**: المتحكم الأساسي (يستخدم AuthorizesRequests)
- **FavoriteController.php**: إدارة المفضلة
- **HomeController.php**: الصفحة الرئيسية
- **ItemController.php**: إدارة العناصر للمستخدمين المسجلين
- **OfferController.php**: إدارة العروض
- **PageController.php**: الصفحات الثابتة
- **ProfileController.php**: الملف الشخصي
- **RequestController.php**: إدارة الطلبات للمستخدمين المسجلين

#### Http/Middleware/ - البرمجيات الوسطية

**الغرض**: معالجة الطلبات قبل وصولها للمتحكمات

- **AddCacheHeaders.php**: إضافة رؤوس التخزين المؤقت
- **EnsureAuthenticatedWithRedirect.php**: التأكد من تسجيل الدخول مع إعادة التوجيه
- **EnsureUserHasRole.php**: التأكد من أن المستخدم لديه دور معين
- **Localization.php**: إدارة اللغة والترجمة

#### Http/Requests/ - طلبات التحقق

**الغرض**: التحقق من صحة البيانات المرسلة

##### Requests/Auth/
- **LoginRequest.php**: التحقق من بيانات تسجيل الدخول

##### Requests/
- **BaseFormRequest.php**: طلب التحقق الأساسي
- **ProfileUpdateRequest.php**: التحقق من تحديث الملف الشخصي
- **StoreItemRequest.php**: التحقق من إنشاء عنصر
- **StoreUserRequest.php**: التحقق من إنشاء مستخدم
- **UpdateItemRequest.php**: التحقق من تحديث عنصر
- **UpdatePasswordRequest.php**: التحقق من تحديث كلمة المرور
- **UpdateUserProfileRequest.php**: التحقق من تحديث ملف المستخدم
- **UpdateUserRequest.php**: التحقق من تحديث مستخدم

---

### 9️⃣ Listeners/ - مستمعي الأحداث

**الغرض**: الاستماع للأحداث وتنفيذ إجراءات عند حدوثها

- **InvalidateItemCache.php**: إبطال التخزين المؤقت للعناصر عند الموافقة/الرفض/الأرشفة
- **InvalidateRequestCache.php**: إبطال التخزين المؤقت للطلبات عند الموافقة/الرفض/الأرشفة
- **LogAdminAction.php**: تسجيل إجراءات المدير (إنشاء/تحديث/حذف مستخدم)

---

### 🔟 Models/ - النماذج

**الغرض**: تمثيل جداول قاعدة البيانات والعلاقات

- **AdminActionLog.php**: سجل إجراءات المدير
- **Approval.php**: الموافقات (polymorphic: Items, Requests)
- **Attribute.php**: الخصائص (الحجم، اللون، الحالة، القماش)
- **AttributeValue.php**: قيم الخصائص
- **Category.php**: الفئات
- **ClothingRequest.php**: طلبات الملابس (قد يكون قديماً)
- **Item.php**: العناصر المعروضة (بيع/إيجار/تبرع)
- **ItemAttribute.php**: ربط العناصر بالخصائص
- **ItemImage.php**: صور العناصر
- **Offer.php**: العروض على الطلبات
- **Product.php**: المنتجات (قد يكون قديماً)
- **Request.php**: الطلبات
- **Setting.php**: إعدادات المنصة
- **User.php**: المستخدمون
- **UserProfile.php**: ملفات المستخدمين الشخصية

---

### 1️⃣1️⃣ Policies/ - السياسات

**الغرض**: تحديد من يمكنه تنفيذ إجراءات معينة (Authorization)

- **AdminActionLogPolicy.php**: صلاحيات الوصول لسجل إجراءات المدير
- **ApprovalPolicy.php**: صلاحيات الموافقة/الرفض
- **AttributePolicy.php**: صلاحيات إدارة الخصائص
- **CategoryPolicy.php**: صلاحيات إدارة الفئات
- **ItemPolicy.php**: صلاحيات إدارة العناصر
- **OfferPolicy.php**: صلاحيات إدارة العروض
- **RequestPolicy.php**: صلاحيات إدارة الطلبات
- **UserPolicy.php**: صلاحيات إدارة المستخدمين

---

### 1️⃣2️⃣ Providers/ - مقدمي الخدمات

**الغرض**: تسجيل الخدمات وإعداد التطبيق

- **AppServiceProvider.php**: مقدم الخدمة الرئيسي
  - تعيين اللغة الافتراضية (العربية)
  - تسجيل مستمعي الأحداث
  - إعداد التخزين المؤقت

- **AuthServiceProvider.php**: مقدم خدمة المصادقة
  - تسجيل السياسات (Policies)

- **Filament/AdminPanelProvider.php**: إعداد لوحة تحكم Filament

- **TelescopeServiceProvider.php**: إعداد Laravel Telescope (للتصحيح)

---

### 1️⃣3️⃣ Read/ - نماذج القراءة (CQRS Pattern)

**الغرض**: نماذج مخصصة للقراءة فقط (Command Query Responsibility Segregation)

#### Read/Items/
- **Models/ItemReadModel.php**: نموذج قراءة العناصر
- **Queries/BrowseItemsQuery.php**: استعلام تصفح العناصر
- **Queries/ViewItemQuery.php**: استعلام عرض عنصر واحد

#### Read/Offers/
- **Models/OfferReadModel.php**: نموذج قراءة العروض

#### Read/Requests/
- **Models/RequestReadModel.php**: نموذج قراءة الطلبات
- **Queries/BrowseRequestsQuery.php**: استعلام تصفح الطلبات
- **Queries/ViewRequestQuery.php**: استعلام عرض طلب واحد

#### Read/Shared/Models/
- **AttributeReadModel.php**: نموذج قراءة الخصائص
- **CategoryReadModel.php**: نموذج قراءة الفئات
- **ImageReadModel.php**: نموذج قراءة الصور
- **UserReadModel.php**: نموذج قراءة المستخدمين

---

### 1️⃣4️⃣ Repositories/ - المستودعات

**الغرض**: طبقة تجريد للوصول إلى قاعدة البيانات

- **BaseRepository.php**: المستودع الأساسي (CRUD عام)
  - `all()`, `find()`, `create()`, `update()`, `delete()`

- **UserRepository.php**: مستودع المستخدمين (عمليات خاصة بالمستخدمين)
- **UserProfileRepository.php**: مستودع ملفات المستخدمين
- **AdminActionLogRepository.php**: مستودع سجل إجراءات المدير

---

### 1️⃣5️⃣ Services/ - الخدمات

**الغرض**: منطق العمل التجاري (Business Logic)

- **BaseService.php**: الخدمة الأساسية (فئة مجردة)

#### Services/Cache/
- **CacheService.php**: خدمة التخزين المؤقت العامة
- **CategoryCacheService.php**: خدمة التخزين المؤقت للفئات
- **PublicCacheService.php**: خدمة التخزين المؤقت للمحتوى العام

#### Services/
- **AdminActionLogService.php**: خدمة تسجيل إجراءات المدير
- **AttributeService.php**: خدمة إدارة الخصائص
- **CategoryService.php**: خدمة إدارة الفئات
- **ImageOptimizationService.php**: خدمة تحسين الصور
- **ItemDeletionService.php**: خدمة حذف العناصر (معالجة معقدة)
- **ItemService.php**: خدمة إدارة العناصر
- **OfferService.php**: خدمة إدارة العروض
- **RequestDeletionService.php**: خدمة حذف الطلبات (معالجة معقدة)
- **RequestService.php**: خدمة إدارة الطلبات
- **RolePermissionService.php**: خدمة إدارة الأدوار والصلاحيات
- **UserService.php**: خدمة إدارة المستخدمين

---

### 1️⃣6️⃣ Traits/ - السمات

**الغرض**: إعادة استخدام الكود عبر النماذج

- **HasApproval.php**: سمة للموافقة (يستخدمها Item, Request)
- **HasAttributes.php**: سمة للخصائص (يستخدمها Item, Request)
- **HasCategory.php**: سمة للفئات (يستخدمها Item, Request)

---

### 1️⃣7️⃣ View/ - مكونات العرض

**الغرض**: مكونات Blade القابلة لإعادة الاستخدام

- **Components/AppLayout.php**: تخطيط التطبيق للمستخدمين المسجلين
- **Components/GuestLayout.php**: تخطيط التطبيق للزوار
- **Components/ItemCard.php**: مكون بطاقة العنصر

---

### 1️⃣8️⃣ ViewModels/ - نماذج العرض

**الغرض**: تحضير البيانات للعرض (فصل المنطق عن العرض)

#### ViewModels/Items/
- **ItemCardViewModel.php**: تحضير بيانات بطاقة العنصر
  - السعر المنسق، الصور، الحالة، الفئة، إلخ

- **ItemDetailViewModel.php**: تحضير بيانات صفحة تفاصيل العنصر
  - جميع معلومات العنصر، الصلاحيات، الروابط، إلخ

#### ViewModels/Profile/
- **ProfileViewModel.php**: تحضير بيانات صفحة الملف الشخصي

#### ViewModels/Requests/
- **RequestCardViewModel.php**: تحضير بيانات بطاقة الطلب
  - الحالة، الموافقة، الخصائص، عدد العروض، إلخ

- **README.md**: توثيق نمط ViewModels

---

## 🎯 ملخص المسؤوليات

### 🔄 تدفق البيانات النموذجي:

1. **HTTP Request** → `Http/Controllers/`
2. **Validation** → `Http/Requests/`
3. **Authorization** → `Policies/`
4. **Business Logic** → `Services/` أو `Actions/`
5. **Data Access** → `Repositories/` أو `Models/`
6. **Events** → `Events/` → `Listeners/`
7. **Data Preparation** → `ViewModels/`
8. **Response** → `View/` أو `resources/views/`

### 📊 الأنماط المعمارية المستخدمة:

- **MVC**: Model-View-Controller
- **Repository Pattern**: لفصل الوصول للبيانات
- **Service Layer**: لمنطق العمل
- **Action Pattern**: للإجراءات المعقدة
- **CQRS**: Command Query Responsibility Segregation (في Read/)
- **ViewModel Pattern**: لفصل المنطق عن العرض
- **Event-Driven**: للأحداث والاستماع
- **Policy Pattern**: للصلاحيات

---

## 📝 ملاحظات مهمة

1. **Actions vs Services**: 
   - Actions: إجراءات محددة ومعزولة (مثل CreateItemAction)
   - Services: منطق عمل أوسع (مثل ItemService)

2. **Read Models**: نماذج مخصصة للقراءة فقط (CQRS) لتحسين الأداء

3. **ViewModels**: جميع الحسابات والتنسيق يتم في ViewModels، وليس في Blade

4. **Events & Listeners**: لإبطال التخزين المؤقت وتسجيل الإجراءات

5. **Filament**: لوحة تحكم إدارية كاملة لإدارة جميع الموارد

---

**آخر تحديث**: يناير 2026  
**الإصدار**: 1.0  
**الحالة**: جاهز للإنتاج ✅
