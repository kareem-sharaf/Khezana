# Phase 5.2: Error Handling و Monitoring

## ما تم إنجازه

### 1. Sentry
- تثبيت `sentry/sentry-laravel`.
- الإعداد عبر `SENTRY_LARAVEL_DSN` في `.env`. إن تركت فارغاً، يتم تعطيل Sentry.
- إرفاق سياق المستخدم (المصادق) عند التسجيل عبر حدث `Authenticated` في `AppServiceProvider`.

### 2. تحسين Exception Handling
- في `bootstrap/app.php` → `withExceptions`: لكل استثناء يُبلَّغ عنه، يتم تسجيله أيضاً في قناة `critical_json` (عند `!APP_DEBUG` أو `LOG_CRITICAL_JSON=true`) مع سياق منظم: `exception`, `message`, `file`, `line`, `url`, `method`, `user_id`.

### 3. Structured Logging
- قناة جديدة `critical_json` في `config/logging.php`:
  - المسار: `storage/logs/critical.json`
  - المستوى: `error`
  - تنسيق: JSON (Monolog `JsonFormatter`).

### 4. Alerts للأخطاء الحرجة
- Sentry يرسل تنبيهات عند حدوث أخطاء عند تفعيله (تعيين `SENTRY_LARAVEL_DSN`).
- يمكن إضافة Slack لقناة `stack` عبر `LOG_SLACK_WEBHOOK_URL` للتنبيهات الحرجة إن رغبت.

### 5. Error Dashboard
- أمر: `php artisan errors:dashboard`
- يقرأ آخر أسطر من `storage/logs/laravel.log`، يفلتر (ERROR, WARNING, CRITICAL, Exception, Slow query)، ويعرض جدولاً لآخر المدخلات مع العدد الإجمالي.
- خيارات:
  - `--lines=50`: عدد أسطر السجل الممسوحة (افتراضي 50).
  - `--show=20`: عدد المدخلات المعروضة في الجدول.
  - `--export=path.json`: تصدير النتائج إلى ملف JSON.

## استخدام Sentry

1. إنشاء مشروع في [sentry.io](https://sentry.io) ونسخ الـ DSN.
2. إضافته في `.env`:
   ```env
   SENTRY_LARAVEL_DSN=https://xxx@xxx.ingest.sentry.io/xxx
   SENTRY_ENVIRONMENT=production
   ```
3. في الإنتاج، يتم إرسال الاستثناءات تلقائياً إلى Sentry مع سياق المستخدم عند المصادقة.

## استخدام Error Dashboard

```bash
php artisan errors:dashboard
php artisan errors:dashboard --lines=200 --show=30
php artisan errors:dashboard --export=storage/app/errors_report.json
```

## الملفات المُعدَّلة/المُضافة

- `composer.json`: إضافة `sentry/sentry-laravel`
- `config/logging.php`: قناة `critical_json`
- `bootstrap/app.php`: `withExceptions` → reportable + تسجيل في `critical_json`
- `app/Providers/AppServiceProvider.php`: مستمع `Authenticated` لسياق Sentry
- `app/Console/Commands/ErrorsDashboardCommand.php`
- `.env.example`: `SENTRY_LARAVEL_DSN`, `LOG_CRITICAL_JSON`

---

**التاريخ**: يناير 2026
