# 🏗️ تقسيم المشروع إلى Domains (Domain-Driven Design)

**التاريخ:** 27 يناير 2026  
**الهدف:** توضيح كيفية تنظيم المشروع بناءً على Domains لتمكين العمل على كل domain بشكل منفصل

---

## 📊 نظرة عامة على البنية

سيتم تقسيم مشروع **خزانة** إلى **9 domains رئيسية**، كل domain يحتوي على:
- Models
- DTOs  
- Services
- Repositories
- Controllers
- Events
- Policies
- Migrations

---

## 🎯 الـ Domains الرئيسية

### 1️⃣ **User Domain** (نظام المستخدمين)
**الوصف:** إدارة المستخدمين والملفات الشخصية والمصادقة

#### الملفات والمسؤوليات:
```
📦 Domains/User/
├── Models/
│   ├── User.php                    # المستخدم الرئيسي
│   └── UserProfile.php             # الملف الشخصي
├── DTOs/
│   ├── UserDTO.php
│   └── UserProfileDTO.php
├── Services/
│   └── UserService.php             # منطق المستخدم
├── Repositories/
│   ├── UserRepository.php
│   └── UserProfileRepository.php
├── Controllers/
│   ├── Auth/
│   │   ├── RegisteredUserController.php
│   │   ├── AuthenticatedSessionController.php
│   │   ├── PasswordController.php
│   │   └── ... (controllers أخرى)
│   └── ProfileController.php
├── Filament/
│   └── UserResource.php            # واجهة الإدارة
├── Policies/
│   └── UserPolicy.php
├── Events/
│   ├── UserCreated.php
│   ├── UserUpdated.php
│   └── UserDeleted.php
├── Requests/
│   └── Validation rules
└── Database/
    ├── Migrations/
    │   ├── create_users_table.php
    │   ├── add_phone_status_to_users_table.php
    │   └── create_user_profiles_table.php
    ├── Factories/
    │   └── UserFactory.php
    └── Seeders/
        └── UsersSeeder.php
```

**الجداول المرتبطة:**
- `users` - البيانات الأساسية
- `user_profiles` - الملفات الشخصية
- `password_reset_tokens` - إعادة تعيين كلمة المرور

**الأحداث:**
- `UserCreated` - عند إنشاء مستخدم
- `UserUpdated` - عند تحديث المستخدم
- `UserDeleted` - عند حذف المستخدم

**الحالة:** ✅ مكتمل

---

### 2️⃣ **Category Domain** (نظام الفئات)
**الوصف:** إدارة فئات المنتجات والخصائص الديناميكية

#### الملفات والمسؤوليات:
```
📦 Domains/Category/
├── Models/
│   ├── Category.php                # الفئات (hierarchical)
│   ├── Attribute.php               # الخصائص
│   ├── AttributeValue.php          # قيم الخصائص
│   └── CategoryAttribute.php        # ربط الفئات بالخصائص
├── DTOs/
│   ├── CategoryDTO.php
│   ├── AttributeDTO.php
│   └── AttributeValueDTO.php
├── Services/
│   ├── CategoryService.php
│   ├── AttributeService.php
│   └── CategoryAttributeService.php
├── Repositories/
│   ├── CategoryRepository.php
│   ├── AttributeRepository.php
│   ├── AttributeValueRepository.php
│   └── CategoryAttributeRepository.php
├── Controllers/
│   ├── CategoryController.php
│   └── AttributeController.php
├── Filament/
│   ├── CategoryResource.php
│   ├── AttributeResource.php
│   └── AttributeValueResource.php
├── Policies/
│   ├── CategoryPolicy.php
│   └── AttributePolicy.php
├── Events/
│   ├── CategoryCreated.php
│   ├── CategoryUpdated.php
│   ├── CategoryDeleted.php
│   ├── AttributeCreated.php
│   └── AttributeDeleted.php
└── Database/
    ├── Migrations/
    │   ├── create_categories_table.php
    │   ├── create_attributes_table.php
    │   ├── create_attribute_values_table.php
    │   └── create_category_attribute_table.php
    └── Seeders/
        └── CategoriesSeeder.php
```

**الجداول المرتبطة:**
- `categories` - الفئات (مع heirarchy)
- `attributes` - الخصائص
- `attribute_values` - قيم الخصائص
- `category_attribute` - ربط الفئات بالخصائص

**الميزات:**
- دعم الفئات الهرمية (Parent-Child)
- خصائص ديناميكية قابلة للتوسع

**الحالة:** ⏳ قيد التطوير

---

### 3️⃣ **Item Domain** (نظام المنتجات/الإعلانات)
**الوصف:** إدارة المنتجات والصور والخصائص الخاصة بكل منتج

#### الملفات والمسؤوليات:
```
📦 Domains/Item/
├── Models/
│   ├── Item.php                    # المنتج الرئيسي
│   ├── ItemImage.php               # صور المنتج
│   └── ItemAttribute.php           # خصائص المنتج (polymorphic)
├── DTOs/
│   ├── ItemDTO.php
│   ├── ItemImageDTO.php
│   └── ItemAttributeDTO.php
├── Services/
│   ├── ItemService.php
│   ├── ItemImageService.php
│   └── ItemAttributeService.php
├── Repositories/
│   ├── ItemRepository.php
│   ├── ItemImageRepository.php
│   └── ItemAttributeRepository.php
├── Controllers/
│   └── ItemController.php
├── Filament/
│   └── ItemResource.php
├── Policies/
│   └── ItemPolicy.php
├── Events/
│   ├── ItemCreated.php
│   ├── ItemUpdated.php
│   ├── ItemDeleted.php
│   ├── ItemApproved.php
│   └── ItemRejected.php
├── Actions/
│   ├── ApproveItemAction.php
│   ├── RejectItemAction.php
│   ├── ArchiveItemAction.php
│   └── PublishItemAction.php
└── Database/
    ├── Migrations/
    │   ├── create_items_table.php
    │   ├── create_item_images_table.php
    │   ├── create_item_attributes_table.php
    │   └── add_columns_to_items_table.php
    └── Seeders/
        └── ItemsSeeder.php
```

**الجداول المرتبطة:**
- `items` - المنتجات
- `item_images` - صور المنتجات
- `item_attributes` - خصائص المنتجات (polymorphic)

**الحالة:** ⏳ قيد التطوير

---

### 4️⃣ **Request Domain** (نظام الطلبات/الاستفسارات)
**الوصف:** إدارة طلبات المستخدمين والبحث عن منتجات

#### الملفات والمسؤوليات:
```
📦 Domains/Request/
├── Models/
│   └── Request.php                 # الطلب
├── DTOs/
│   └── RequestDTO.php
├── Services/
│   └── RequestService.php
├── Repositories/
│   └── RequestRepository.php
├── Controllers/
│   └── RequestController.php
├── Filament/
│   └── RequestResource.php
├── Policies/
│   └── RequestPolicy.php
├── Events/
│   ├── RequestCreated.php
│   ├── RequestUpdated.php
│   ├── RequestDeleted.php
│   ├── RequestApproved.php
│   ├── RequestRejected.php
│   └── RequestFulfilled.php
├── Actions/
│   ├── ApproveRequestAction.php
│   ├── RejectRequestAction.php
│   └── FulfillRequestAction.php
└── Database/
    ├── Migrations/
    │   ├── create_requests_table.php
    │   └── add_columns_to_requests_table.php
    └── Seeders/
        └── RequestsSeeder.php
```

**الجداول المرتبطة:**
- `requests` - الطلبات

**العلاقات:**
- كل طلب لمستخدم واحد (User)
- كل طلب في فئة واحدة (Category)
- طلب واحد له عروض متعددة (Offers)

**الحالة:** ⏳ قيد التطوير

---

### 5️⃣ **Offer Domain** (نظام العروض)
**الوصف:** إدارة العروض على الطلبات والمنتجات

#### الملفات والمسؤوليات:
```
📦 Domains/Offer/
├── Models/
│   └── Offer.php                   # العرض
├── DTOs/
│   └── OfferDTO.php
├── Services/
│   └── OfferService.php
├── Repositories/
│   └── OfferRepository.php
├── Controllers/
│   └── OfferController.php
├── Filament/
│   └── OfferResource.php
├── Policies/
│   └── OfferPolicy.php
├── Events/
│   ├── OfferCreated.php
│   ├── OfferUpdated.php
│   ├── OfferAccepted.php
│   ├── OfferRejected.php
│   └── OfferCancelled.php
├── Actions/
│   ├── AcceptOfferAction.php
│   ├── RejectOfferAction.php
│   └── CancelOfferAction.php
└── Database/
    ├── Migrations/
    │   ├── create_offers_table.php
    │   └── add_columns_to_offers_table.php
    └── Seeders/
        └── OffersSeeder.php
```

**الجداول المرتبطة:**
- `offers` - العروض

**الحالة:** ⏳ قيد التطوير

---

### 6️⃣ **Order Domain** (نظام الطلبيات/الشراء)
**الوصف:** إدارة الطلبيات الفعلية والدفع والشحن

#### الملفات والمسؤوليات:
```
📦 Domains/Order/
├── Models/
│   ├── Order.php                   # الطلبية الرئيسية
│   ├── OrderItem.php               # عناصر الطلبية
│   ├── OrderQrCode.php             # رمز QR
│   ├── OrderTracking.php           # تتبع التغييرات
│   └── StoreTransaction.php        # المعاملات المالية
├── DTOs/
│   ├── OrderDTO.php
│   ├── OrderItemDTO.php
│   ├── OrderQrCodeDTO.php
│   ├── OrderTrackingDTO.php
│   └── StoreTransactionDTO.php
├── Services/
│   ├── OrderService.php
│   ├── OrderQrCodeService.php
│   ├── OrderTrackingService.php
│   └── StoreTransactionService.php
├── Repositories/
│   ├── OrderRepository.php
│   ├── OrderItemRepository.php
│   ├── OrderQrCodeRepository.php
│   ├── OrderTrackingRepository.php
│   └── StoreTransactionRepository.php
├── Controllers/
│   ├── OrderController.php
│   ├── OrderQrCodeController.php
│   └── StorePickupController.php
├── Filament/
│   ├── OrderResource.php
│   ├── OrderTrackingResource.php
│   └── StoreTransactionResource.php
├── Policies/
│   └── OrderPolicy.php
├── Events/
│   ├── OrderCreated.php
│   ├── OrderStatusChanged.php
│   ├── OrderPickedUp.php
│   ├── OrderShipped.php
│   ├── OrderDelivered.php
│   ├── OrderCancelled.php
│   ├── PaymentConfirmed.php
│   ├── QrCodeGenerated.php
│   └── QrCodeScanned.php
├── Actions/
│   ├── CreateOrderAction.php
│   ├── GenerateQrCodeAction.php
│   ├── ScanQrCodeAction.php
│   ├── CompletePickupAction.php
│   └── CancelOrderAction.php
├── Enums/
│   ├── OrderStatus.php
│   ├── OrderItemStatus.php
│   ├── TrackingEventType.php
│   └── ActorType.php
└── Database/
    ├── Migrations/
    │   ├── create_orders_table.php
    │   ├── create_order_items_table.php
    │   ├── create_order_qr_codes_table.php
    │   ├── create_order_trackings_table.php
    │   └── create_store_transactions_table.php
    └── Seeders/
        └── OrdersSeeder.php
```

**الجداول المرتبطة:**
- `orders` - الطلبيات
- `order_items` - عناصر الطلبية
- `order_qr_codes` - رموز QR
- `order_trackings` - تتبع التغييرات
- `store_transactions` - المعاملات المالية

**الحالة:** ✅ مكتمل (جزئياً)

---

### 7️⃣ **Approval Domain** (نظام المعتمديات)
**الوصف:** إدارة الموافقات على المنتجات والطلبات

#### الملفات والمسؤوليات:
```
📦 Domains/Approval/
├── Models/
│   └── Approval.php                # المعتمدية (polymorphic)
├── DTOs/
│   └── ApprovalDTO.php
├── Services/
│   └── ApprovalService.php
├── Repositories/
│   └── ApprovalRepository.php
├── Controllers/
│   └── ApprovalController.php
├── Filament/
│   └── ApprovalResource.php
├── Policies/
│   └── ApprovalPolicy.php
├── Events/
│   ├── ApprovalCreated.php
│   ├── ApprovalApproved.php
│   ├── ApprovalRejected.php
│   └── ApprovalCancelled.php
├── Actions/
│   ├── ApproveAction.php
│   └── RejectAction.php
├── Enums/
│   └── ApprovalStatus.php
└── Database/
    ├── Migrations/
    │   ├── create_approvals_table.php
    │   └── add_columns_to_approvals_table.php
    └── Seeders/
        └── ApprovalsSeeder.php
```

**الجداول المرتبطة:**
- `approvals` - المعتمديات (Polymorphic)

**العلاقات:**
- يمكن الموافقة على Items أو Requests
- polymorphic: `approvable_type` و `approvable_id`

**الحالة:** ⏳ قيد التطوير

---

### 8️⃣ **Branch Domain** (نظام الفروع)
**الوصف:** إدارة الفروع ومراكز الفحص والمتاجر

#### الملفات والمسؤوليات:
```
📦 Domains/Branch/
├── Models/
│   ├── Branch.php                  # الفرع
│   └── InspectionCenter.php        # مركز الفحص/المتجر
├── DTOs/
│   ├── BranchDTO.php
│   └── InspectionCenterDTO.php
├── Services/
│   ├── BranchService.php
│   └── InspectionCenterService.php
├── Repositories/
│   ├── BranchRepository.php
│   └── InspectionCenterRepository.php
├── Controllers/
│   ├── BranchController.php
│   └── InspectionCenterController.php
├── Filament/
│   ├── BranchResource.php
│   └── InspectionCenterResource.php
├── Policies/
│   ├── BranchPolicy.php
│   └── InspectionCenterPolicy.php
├── Events/
│   ├── BranchCreated.php
│   ├── BranchUpdated.php
│   ├── BranchDeleted.php
│   ├── InspectionCenterCreated.php
│   └── InspectionCenterUpdated.php
└── Database/
    ├── Migrations/
    │   ├── create_branches_table.php
    │   ├── create_inspection_centers_table.php
    │   └── add_columns_to_branches_table.php
    └── Seeders/
        ├── BranchesSeeder.php
        └── InspectionCentersSeeder.php
```

**الجداول المرتبطة:**
- `branches` - الفروع
- `inspection_centers` - مراكز الفحص/المتاجر

**الحالة:** ⏳ قيد التطوير

---

### 9️⃣ **Admin Domain** (نظام الإدارة والسجلات)
**الوصف:** إدارة السجلات الإدارية والإجراءات والإعدادات

#### الملفات والمسؤوليات:
```
📦 Domains/Admin/
├── Models/
│   ├── AdminActionLog.php          # سجل الإجراءات
│   └── Setting.php                 # الإعدادات
├── DTOs/
│   ├── AdminActionLogDTO.php
│   └── SettingDTO.php
├── Services/
│   ├── AdminActionLogService.php
│   ├── AuditService.php
│   └── SettingService.php
├── Repositories/
│   ├── AdminActionLogRepository.php
│   └── SettingRepository.php
├── Controllers/
│   ├── AdminActionLogController.php
│   ├── DashboardController.php
│   └── SettingsController.php
├── Filament/
│   ├── AdminActionLogResource.php
│   ├── SettingsResource.php
│   └── DashboardWidget.php
├── Policies/
│   ├── AdminActionLogPolicy.php
│   └── SettingPolicy.php
├── Events/
│   ├── AdminActionLogged.php
│   └── SettingChanged.php
├── Enums/
│   └── ActionType.php
└── Database/
    ├── Migrations/
    │   ├── create_admin_actions_logs_table.php
    │   └── create_settings_table.php
    └── Seeders/
        └── SettingsSeeder.php
```

**الجداول المرتبطة:**
- `admin_actions_logs` - سجلات الإجراءات
- `settings` - الإعدادات العامة

**الحالة:** ⏳ قيد التطوير

---

## 🔗 خريطة العلاقات بين الـ Domains

```
┌─────────────────────────────────────────────────────────────┐
│                     SHARED KERNEL                           │
│  - Authentication/Authorization                             │
│  - Base Services, Repositories, Traits                      │
│  - Shared Events, Enums, DTOs                               │
└─────────────────────────────────────────────────────────────┘

                          │
        ┌─────────────────┼─────────────────┐
        │                 │                 │
        ▼                 ▼                 ▼
   ┌─────────┐      ┌─────────┐      ┌──────────┐
   │  User   │      │Category │      │  Branch  │
   │ Domain  │      │ Domain  │      │  Domain  │
   └─────────┘      └─────────┘      └──────────┘
        │                 │                 │
        │                 ▼                 │
        │            ┌─────────┐            │
        │            │  Item   │            │
        │            │ Domain  │            │
        │            └─────────┘            │
        │                 │                 │
        │    ┌────────────┼────────────┐    │
        │    │            │            │    │
        │    ▼            ▼            ▼    │
        │ ┌─────┐   ┌─────────┐   ┌────┐   │
        │ │Offer│   │ Request │   │Order├──┘
        │ │Domain│   │ Domain  │   │Domain│
        │ └─────┘   └─────────┘   └────┘
        │    │            │            │
        │    └────────────┼────────────┘
        │                 │
        │                 ▼
        │            ┌──────────┐
        │            │Approval  │
        │            │Domain    │
        │            └──────────┘
        │                 │
        └────────────────┬┘
                         │
                         ▼
                  ┌──────────────┐
                  │Admin Domain  │
                  │(Logging/Audit)
                  └──────────────┘
```

### شرح العلاقات:

1. **User Domain** ← Core Foundation
   - جميع الـ domains تعتمد على المستخدمين
   
2. **Category Domain** ← Foundational
   - تصنيف المنتجات والطلبات
   
3. **Branch Domain** ← Operational
   - دعم العمليات على الفروع
   
4. **Item Domain** ← Core Business
   - يعتمد على Category و User
   
5. **Request/Offer/Order Domains** ← Business Logic
   - Request و Offer يعتمدان على Item
   - Order يعتمد على Item و User و Branch
   
6. **Approval Domain** ← Cross-Cutting
   - يغطي Item و Request (polymorphic)
   
7. **Admin Domain** ← Supporting
   - يسجل أنشطة جميع الـ domains

---

## 📁 البنية المقترحة للمجلدات

```
app/
├── Domains/
│   ├── User/
│   │   ├── Models/
│   │   ├── DTOs/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Controllers/
│   │   ├── Http/Requests/
│   │   ├── Filament/
│   │   ├── Policies/
│   │   ├── Events/
│   │   ├── Actions/
│   │   ├── Enums/
│   │   └── Database/
│   │       ├── Migrations/
│   │       ├── Factories/
│   │       └── Seeders/
│   ├── Category/
│   │   └── (نفس البنية)
│   ├── Item/
│   │   └── (نفس البنية)
│   ├── Request/
│   │   └── (نفس البنية)
│   ├── Offer/
│   │   └── (نفس البنية)
│   ├── Order/
│   │   └── (نفس البنية)
│   ├── Approval/
│   │   └── (نفس البنية)
│   ├── Branch/
│   │   └── (نفس البنية)
│   └── Admin/
│       └── (نفس البنية)
├── Shared/
│   ├── Traits/
│   ├── Services/ (BaseService, etc)
│   ├── Events/ (Shared events)
│   ├── Enums/ (Shared enums)
│   ├── Exceptions/
│   ├── Helpers/
│   └── Support/
├── Contracts/
│   ├── Approvable.php
│   ├── Trackable.php
│   └── ...
└── ... (البنية الأخرى)
```

---

## 🚀 خطة العمل المقترحة

### المرحلة 1: الأساس (الأسبوع 1-2)
- ✅ **User Domain** (موجود)
  - المصادقة والملفات الشخصية
  
- 🔨 **Category Domain** (Foundational)
  - الفئات والخصائص
  - التصنيفات الهرمية

### المرحلة 2: المنتجات (الأسبوع 3-4)
- 🔨 **Item Domain**
  - المنتجات والصور
  - الخصائص الديناميكية
  
- 🔨 **Approval Domain**
  - الموافقة على المنتجات

### المرحلة 3: الطلبات والعروض (الأسبوع 5-6)
- 🔨 **Request Domain**
  - الطلبات/الاستفسارات
  
- 🔨 **Offer Domain**
  - العروض على الطلبات

### المرحلة 4: الطلبيات والدفع (الأسبوع 7-8)
- ✅ **Order Domain** (موجود جزئياً)
  - الطلبيات الفعلية
  - QR Codes
  - المعاملات المالية

### المرحلة 5: العمليات الإدارية (الأسبوع 9-10)
- 🔨 **Branch Domain**
  - الفروع ومراكز الفحص
  
- 🔨 **Admin Domain**
  - السجلات والإعدادات

---

## 📝 نموذج الملف لكل Domain

### المسار الموحد:
```
app/Domains/{DomainName}/{Entity}/{Type}/
```

### مثال - User Domain:
```
app/Domains/User/
├── Models/User.php
├── DTOs/UserDTO.php
├── Services/UserService.php
├── Repositories/UserRepository.php
├── Controllers/Auth/RegisteredUserController.php
├── Http/Requests/RegisterUserRequest.php
├── Filament/Resources/UserResource.php
├── Policies/UserPolicy.php
├── Events/UserCreated.php
├── Actions/CreateUserAction.php
├── Enums/UserStatus.php
└── Database/
    ├── Migrations/2026_01_01_000000_create_users_table.php
    ├── Factories/UserFactory.php
    └── Seeders/UsersSeeder.php
```

---

## 🔄 الفوائد المتوقعة

### 1. **Modularity** - التعديل المستقل
- يمكن تطوير كل domain بشكل مستقل
- لا يوجد تعارضات في الملفات

### 2. **Scalability** - التوسعية
- سهل إضافة features جديدة
- الكود منظم وسهل الفهم

### 3. **Maintainability** - السهولة في الصيانة
- كل domain له مسؤوليات واضحة
- سهل إيجاد الأخطاء وتصحيحها

### 4. **Testing** - الاختبار
- اختبار كل domain بشكل منفصل
- عزل التبعيات

### 5. **Team Collaboration** - تعاون الفريق
- كل فريق يعمل على domain واحد
- تقليل التضارب في الـ merge

### 6. **Documentation** - التوثيق
- توثيق واضح لكل domain
- سهل فهم العلاقات بينها

---

## 📊 جدول المقارنة: قبل وبعد

| المعيار | قبل | بعد |
|--------|------|------|
| **هيكل المشروع** | مختلط | منظم وموحد |
| **البحث عن الملفات** | صعب | سهل جداً |
| **العمل الجماعي** | يسبب تضارب | سلس |
| **الاختبار** | كامل أو لا شيء | اختبار كل domain |
| **الإعادة البناء** | صعب | آمن |
| **التوسع** | يسبب فوضى | منطقي |

---

## 🎯 الملخص

**التقسيم المقترح:**
1. ✅ User Domain (جاهز)
2. 🔨 Category Domain
3. 🔨 Item Domain
4. 🔨 Request Domain
5. 🔨 Offer Domain
6. ✅ Order Domain (جزئي)
7. 🔨 Approval Domain
8. 🔨 Branch Domain
9. 🔨 Admin Domain

**الفائدة الرئيسية:**
✨ يمكنك التركيز على domain واحد في المرة، بدون أن تقلق بشأن بقية الـ domains

---

## 📞 الملاحظات الإضافية

### كيفية البدء:
1. انسخ بنية User Domain كـ template
2. أنشئ folder جديد لكل domain
3. ابدأ بـ Category Domain (يعتمد على نفسه)
4. ثم Item Domain (يعتمد على Category و User)
5. وهكذا...

### التعامل مع العلاقات بين Domains:
- استخدم **Events** للتواصل بين الـ domains
- تجنب التبعيات المباشرة بين Services
- استخدم **DTOs** للنقل الآمن للبيانات

### الحفاظ على النظام:
- كل domain يجب أن يكون مستقل
- Shared Kernel فقط يحتوي على الكود المشترك
- توثيق واضحة للعلاقات

---

**تم إعداد هذا الدليل لتنظيم العمل وتسهيل التطوير بشكل منهجي 🚀**
