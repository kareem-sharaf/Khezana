# دليل التثبيت والمتطلبات

## 📋 المتطلبات

- PHP 8.1+
- Laravel 10+
- Composer
- SQLite/MySQL
- Sanctum (للـ API authentication)

## ⚡ خطوات التثبيت السريع

### 1️⃣ تثبيت المتطلبات

```bash
# تثبيت QR Code
composer require simplesoftwareio/simple-qrcode

# تثبيت Sanctum (إن لم يكن مثبتاً)
composer require laravel/sanctum
```

### 2️⃣ تشغيل الترحيلات

```bash
# تشغيل جميع الترحيلات
php artisan migrate

# أو تشغيل ترحيل محدد
php artisan migrate --path=database/migrations/2026_01_26_100000_create_orders_table.php
```

### 3️⃣ إنشاء Storage Link

```bash
# لتخزين صور QR
php artisan storage:link
```

### 4️⃣ تسجيل الأحداث (اختياري)

إذا كنت تريد اكتشاف الأحداث تلقائياً:

```php
// في app/Providers/EventServiceProvider.php
public function shouldDiscoverEvents(): bool
{
    return true; // أو false للتحكم اليدوي
}
```

### 5️⃣ إعداد المهام المجدولة

#### أ) تعديل Kernel.php

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // التحقق من الطلبات المنتهية كل 5 دقائق
    $schedule->command('orders:check-expired')->everyFiveMinutes();
    
    // إرسال التذكيرات كل ساعة
    $schedule->command('notifications:send-pickup-reminders')->hourly();
}
```

#### ب) تشغيل Cron

للأنظمة الحقيقية، أضف هذا إلى crontab:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

### 6️⃣ اختبار التثبيت

```bash
# تشغيل جميع الاختبارات
php artisan test

# أو اختبار النظام فقط
php artisan test tests/Feature/OrderSystemTest.php

# مع التفاصيل
php artisan test --verbose
```

---

## 🔧 الإعدادات الإضافية

### إعداد Sanctum (للـ API)

```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ',' . parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

### إعداد Queue (اختياري للإشعارات الفورية)

```php
// .env
QUEUE_CONNECTION=database

// ثم
php artisan queue:table
php artisan migrate
```

### إعداد Mail (لإرسال البريد)

```php
// .env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=no-reply@khezana.com
MAIL_FROM_NAME="خزانة"
```

---

## 🚀 تشغيل الخادم

### التطوير

```bash
# Terminal 1: تشغيل الخادم
php artisan serve

# Terminal 2: تشغيل المهام المجدولة
php artisan schedule:work

# Terminal 3 (اختياري): معالج الرسائل
php artisan queue:work
```

### الإنتاج

استخدم Supervisor أو استضافة الويب المدارة.

---

## 📝 اختبار الـ API

### مع cURL

```bash
# 1. تسجيل الدخول والحصول على TOKEN
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# 2. إنشاء طلب (باستخدام TOKEN)
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "channel": "IN_STORE_PICKUP",
    "items": [{"item_id": 1, "operation_type": "SALE"}],
    "pickup_store_id": 1,
    "payment_method": "CASH_IN_STORE"
  }'

# 3. الحصول على الطلب
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### مع Postman

1. استورد `routes/api.php` في Postman
2. عيّن المتغيرات:
   - `{{base_url}}` = `http://localhost:8000`
   - `{{token}}` = الرمز من تسجيل الدخول
3. شغّل الطلبات

---

## ✅ قائمة التحقق

- [ ] تثبيت المتطلبات
- [ ] تشغيل الترحيلات
- [ ] إنشاء Storage Link
- [ ] إعداد المهام المجدولة
- [ ] اختبار الـ API
- [ ] التحقق من الأمان والصلاحيات
- [ ] إعداد الإخطارات (واتساب/بريد)
- [ ] نسخ احتياطية من البيانات

---

## 🐛 استكشاف الأخطاء

### خطأ: "Class not found"

```bash
# أعد تحميل التصنيفات
composer dump-autoload
```

### خطأ: "Migration not found"

```bash
# تحقق من أسماء الملفات
ls database/migrations/2026*

# أعد تشغيل الترحيلات
php artisan migrate:refresh
```

### خطأ: "Storage permission denied"

```bash
# صحح الصلاحيات
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### خطأ: "QR Code not generating"

```bash
# تحقق من وجود المكتبة
composer show simplesoftwareio/simple-qrcode

# أعد التثبيت
composer update simplesoftwareio/simple-qrcode
```

---

## 📊 التحقق من التثبيت

```bash
# اختبر الاتصال بقاعدة البيانات
php artisan db:show

# اختبر الترحيلات
php artisan migrate:status

# اختبر الخدمات
php artisan tinker
> Order::count()  // يجب أن تحصل على 0 أو أكثر

# اختبر الأوامر
php artisan list | grep orders
php artisan list | grep notifications
```

---

## 🎯 الخطوات التالية

بعد التثبيت بنجاح:

1. **قراءة الوثائق:**
   - `DATABASE_SCHEMA.md` - فهم هيكل البيانات
   - `ORDERS_SYSTEM.md` - فهم تدفق النظام
   - `IMPLEMENTATION_SUMMARY.md` - ملخص الإنجازات

2. **تطوير الواجهات:**
   - `resources/views/orders/create.blade.php`
   - `resources/views/orders/show.blade.php`
   - `resources/views/store/pickup.blade.php`

3. **إضافة المميزات:**
   - نظام الدفع
   - تكامل واتساب
   - لوحة التحكم

4. **الأمان:**
   - تفعيل HTTPS
   - إعداد Firewall
   - نسخ احتياطية منتظمة

---

## 📞 الدعم

إذا واجهت مشاكل:

1. افحص ملفات الأخطاء: `storage/logs/laravel.log`
2. اقرأ رسائل الأخطاء بعناية
3. تحقق من صحة البيانات المدخلة
4. استخدم `php artisan tinker` للاختبار

---

## 🎉 أنت الآن جاهز!

النظام مُثبت وجاهز للاستخدام! ابدأ بـ:

```bash
php artisan serve
```

ثم زُر: `http://localhost:8000`
