# Phase 7: قابلية التوسع (Scalability)

## الهدف
تهيئة البنية لتحمّل نمو المستخدمين وتقليل الأعطال (قراءة مكررة، موازنة حمل، Queue).

---

## 7.1 Database Read Replicas

### متى تُستخدم
- عندما يصبح الحمل على القراءة مرتفعاً وتستدعي الاستعلامات فصل القراءة عن الكتابة.

### إعداد Laravel

1. **`config/database.php`** – إضافة اتصالات للـ replica:

```php
'mysql' => [
    'read' => [
        'host' => [env('DB_READ_HOST', '127.0.0.1')],
    ],
    'write' => [
        'host' => [env('DB_WRITE_HOST', '127.0.0.1')],
    ],
    'sticky' => true,
    'driver' => 'mysql',
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
],
```

2. **`.env`**:
```env
DB_READ_HOST=replica.db.example.com
DB_WRITE_HOST=primary.db.example.com
```

3. استخدام **Read Replica** فعلياً يتطلب خادم MySQL مُعدّاً كـ replica (replication).

---

## 7.2 Load Balancer و Horizontal Scaling

### مبادئ

- جعل التطبيق **stateless**: عدم الاعتماد على ملفات الجلسة المحلية.
- تخزين الجلسات في **Redis** (أو DB):

```env
SESSION_DRIVER=redis
```

- استخدام **Load Balancer** (Nginx، HAProxy، أو managed) أمام عدة instances من التطبيق.
- **Health check**: endpoint مثل `/up` (Laravel `Route::get('/up', ...)`) للتحقق من سلامة كل instance.

### Redis للجلسات

```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
SESSION_DRIVER=redis
```

---

## 7.3 Queue System المتقدم

### عمال متعددون

```bash
# عدة عمّال في خلفية منفصلة
php artisan queue:work --queue=default,emails --tries=3 &
php artisan queue:work --queue=default,emails --tries=3 &
```

أو استخدام **Supervisor** (أو systemd) لتشغيل عدة عمّال وإعادة تشغيلهم عند التعطل.

### أولويات الـ Queue

```php
// إرسال إلى queue محدّد
ProcessItemImagesJob::dispatch($itemId, $paths)->onQueue('high');
```

تشغيل عمّال يراعون الأولوية:

```bash
php artisan queue:work --queue=high,default
```

### Failed Jobs

```bash
php artisan queue:failed          # عرض فاشلة
php artisan queue:retry all      # إعادة محاولة الكل
php artisan queue:retry <id>     # إعادة محاولة واحدة
php artisan queue:flush         # حذف كل الفاشلة
```

### مراقبة الـ Queue

- **Laravel Telescope** (إن مُفعّل) لمراقبة الـ jobs.
- **`php artisan queue:monitor redis:default,redis:high`** (إن مُتاح) أو سكربتات مخصصة لمراقبة الطول والفشل.

---

## الخلاصة

| المكوّن | الإعداد |
|---------|---------|
| Read Replicas | `config/database.php` + `DB_READ_HOST` / `DB_WRITE_HOST` |
| Sessions | `SESSION_DRIVER=redis` |
| Load Balancer | Nginx/HAProxy + Health check على `/up` |
| Queue | عدة عمّال، أولويات، `queue:failed` / `queue:retry` |

---

**التاريخ**: يناير 2026
