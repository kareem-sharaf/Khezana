# دليل نظام الطلبات والاستلام من المتجر

## 📋 نظرة عامة

هذا النظام يوفر:
- **إنشاء الطلبات**: طلبات توصيل أو استلام من المتجر
- **QR Codes**: توليد وإدارة رموز QR للاستلام الآمن
- **تتبع الطلبات**: تتبع تفصيلي لحالة كل طلب
- **إدارة المنتجات**: حجز وإطلاق المنتجات تلقائياً
- **إخطارات عبر واتساب**: إرسال QR والتذكيرات

---

## 🗂️ هيكل المشروع

### Models
- **Order** - الطلب الرئيسي
- **OrderItem** - عنصر في الطلب
- **OrderQrCode** - رمز QR للاستلام
- **OrderTracking** - تتبع التغييرات
- **StoreTransaction** - معاملات المتجر

### Services
- **OrderService** - معالجة الطلبات
- **QrService** - إدارة رموز QR
- **StorePickupService** - عمليات الاستلام
- **NotificationService** - الإخطارات

### Controllers
- **OrderController** - API الطلبات
- **StorePickupController** - API الاستلام

### Events & Listeners
- **OrderCreated** → SendOrderConfirmationNotification
- **OrderCancelled** → NotifyOrderCancellation
- **OrderPickupCompleted** → NotifyPickupCompleted

### Console Commands
- **orders:check-expired** - التحقق من الطلبات المنتهية (كل 5 دقائق)
- **notifications:send-pickup-reminders** - إرسال التذكيرات (كل ساعة)

---

## 🚀 دليل الاستخدام

### 1. إنشاء طلب جديد

```bash
POST /api/orders
Content-Type: application/json
Authorization: Bearer TOKEN

{
  "channel": "IN_STORE_PICKUP",
  "items": [
    {
      "item_id": 1,
      "operation_type": "SALE",
      "unit_price": 100.00
    }
  ],
  "pickup_store_id": 1,
  "payment_method": "CASH_IN_STORE"
}
```

**الرد:**
```json
{
  "success": true,
  "message": "تم إنشاء الطلب بنجاح",
  "data": {
    "order_id": 1,
    "order_number": "ORD-20260126-00001",
    "qr_code": "QR-xxxxx",
    "total_amount": 100.00,
    "channel": "IN_STORE_PICKUP"
  }
}
```

### 2. الحصول على تفاصيل الطلب

```bash
GET /api/orders/1
Authorization: Bearer TOKEN
```

### 3. التحقق من QR في المتجر

```bash
POST /api/store/verify-qr
Content-Type: application/json
Authorization: Bearer TOKEN

{
  "qr_code": "QR-xxxxx"
}
```

**الرد:**
```json
{
  "success": true,
  "message": "تم التحقق من الكود بنجاح",
  "data": {
    "order_id": 1,
    "order_number": "ORD-20260126-00001",
    "customer_name": "أحمد محمد",
    "customer_phone": "+966501234567",
    "items": [...],
    "total_amount": 100.00
  }
}
```

### 4. إكمال الاستلام

```bash
POST /api/store/complete-pickup
Content-Type: application/json
Authorization: Bearer TOKEN

{
  "order_id": 1,
  "payment_amount": 100.00,
  "payment_method": "CASH_IN_STORE",
  "notes": "تم الاستلام بنجاح"
}
```

### 5. إلغاء الطلب

```bash
POST /api/orders/1/cancel
Content-Type: application/json
Authorization: Bearer TOKEN

{
  "reason": "غيرت رأيي"
}
```

### 6. قائمة طلبات المستخدم

```bash
GET /api/user/orders
Authorization: Bearer TOKEN
```

---

## 📊 تدفق العملية

### لطلب الاستلام من المتجر:

```
1. العميل ينشئ طلب
   ↓
2. يتم حجز المنتجات تلقائياً
   ↓
3. يتم توليد QR Code
   ↓
4. إرسال QR عبر واتساب
   ↓
5. العميل يصل للمتجر
   ↓
6. الموظف يمسح QR
   ↓
7. عرض تفاصيل الطلب
   ↓
8. تأكيد الاستلام
   ↓
9. تحديث حالة المنتجات إلى SOLD/RENTED
   ↓
10. إرسال إشعار النجاح
```

---

## ⚙️ المهام المجدولة

### التحقق من الطلبات المنتهية
```bash
* * * * * php artisan orders:check-expired
```

يقوم بـ:
- البحث عن طلبات انتهت صلاحيتها (أكثر من 24 ساعة)
- تغيير حالة الطلب إلى EXPIRED
- إطلاق المنتجات المحجوزة
- تعليم QR كمنتهي
- إرسال إشعار للعميل

### إرسال التذكيرات
```bash
0 * * * * php artisan notifications:send-pickup-reminders
```

يقوم بـ:
- البحث عن طلبات تنتهي خلال 6 ساعات
- إرسال تذكير واتساب
- سجل المحاولات

---

## 🔐 حالات الطلب

```
CREATED         ← تم الإنشاء للتو
    ↓
PENDING_PAYMENT ← في انتظار الدفع (للتوصيل فقط)
    ↓
CONFIRMED       ← تم التأكيد (للاستلام من المتجر)
    ↓
PROCESSING      ← قيد المعالجة
    ↓
READY_FOR_PICKUP← جاهز للاستلام
    ↓
CUSTOMER_ARRIVED← وصل العميل للمتجر
    ↓
COMPLETED       ← مكتمل ✅
    ✗ CANCELLED  ← ملغي
    ✗ EXPIRED    ← منتهي الصلاحية
```

---

## 📦 حالات المنتج

```
AVAILABLE       ← متاح للبيع
    ↓
RESERVED        ← محجوز (عند إنشاء الطلب)
    ↓
SOLD/RENTED     ← مباع / مؤجر (عند إكمال الاستلام)
    ✗ CANCELLED  ← ملغي (عند إلغاء الطلب)
```

---

## 🔧 إعداد النظام

### 1. تثبيت المتطلبات

```bash
composer require simplesoftwareio/simple-qrcode
```

### 2. تشغيل الترحيلات

```bash
php artisan migrate
```

### 3. إنشاء Storage Link

```bash
php artisan storage:link
```

### 4. تسجيل Commands في Scheduler

في `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('orders:check-expired')->everyFiveMinutes();
    $schedule->command('notifications:send-pickup-reminders')->hourly();
}
```

---

## 🧪 الاختبارات

### إنشاء طلب توصيل

```php
$order = Order::create([
    'customer_id' => $user->id,
    'channel' => 'DELIVERY',
    'status' => 'PENDING_PAYMENT',
    'delivery_address' => 'شارع النيل',
    'delivery_city' => 'الرياض',
]);
```

### إنشاء طلب استلام من المتجر

```php
$order = Order::create([
    'customer_id' => $user->id,
    'channel' => 'IN_STORE_PICKUP',
    'status' => 'CONFIRMED',
    'pickup_store_id' => $store->id,
    'pickup_expiry' => now()->addHours(24),
]);
```

### توليد QR

```php
$qrService = app(QrService::class);
$qrCode = $qrService->generateQrCode($order);
```

### التحقق من QR

```php
$qrService = app(QrService::class);
$valid = $qrService->validateQrCode($qrCode->qr_code);
```

### إكمال الاستلام

```php
$service = app(StorePickupService::class);
$service->completePickup($order, $staffUser->id);
```

---

## 📧 تكامل واتساب

يتم التكامل عبر:
- **Twilio** - للواتساب
- **MessageBird** - بديل
- **WhatsApp Business API** - الخيار الأفضل

### مثال Twilio:

```php
// في NotificationService
private function sendWhatsAppMessage(string $phone, string $message)
{
    try {
        $twilio = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );

        $twilio->messages->create(
            'whatsapp:' . $phone,
            ['from' => config('services.twilio.whatsapp_number')],
            ['body' => $message]
        );
    } catch (\Exception $e) {
        Log::error('WhatsApp Send Failed', ['error' => $e->getMessage()]);
    }
}
```

---

## 🛡️ الأمان

### حماية QR Codes
- توقيع رقمي SHA-256
- صلاحية 24 ساعة فقط
- استخدام لمرة واحدة
- تسجيل كل عملية مسح

### التحقق من الصلاحيات
- العملاء: فقط طلباتهم
- موظفو المتجر: طلبات متجرهم فقط
- الإدارة: جميع الطلبات

### منع التلاعب
- التحقق من توفر المنتج قبل الحجز
- منع الحجز المزدوج
- تسجيل كل تغيير

---

## 📞 الدعم

### الأخطاء الشائعة

1. **"رمز QR غير صحيح"**
   - تأكد من نسخ الرمز كاملاً
   - تحقق من عدم انتهاء الصلاحية

2. **"المنتج غير متاح"**
   - قد يكون محجوزاً بطلب آخر
   - حاول لاحقاً

3. **"غير مصرح لك"**
   - تأكد من تسجيل الدخول
   - تحقق من الصلاحيات

---

## 📝 ملاحظات مهمة

- ✅ جميع المنتجات يجب أن تكون معتمدة أولاً
- ✅ لا يوجد فرق في السعر بين التطبيق والمتجر
- ✅ نفس السعر في كل الطرق
- ✅ لا توجد رسوم إضافية مخفية
- ✅ تأكيد الاستلام بسيط وسريع
