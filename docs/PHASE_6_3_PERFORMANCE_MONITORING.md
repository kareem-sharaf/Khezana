# Phase 6.3: Performance Monitoring المتقدم

## ما تم إنجازه

### 1. Performance Dashboard
- أمر **`php artisan performance:dashboard`**: يعرض جدولاً بالمؤشرات (حجم DB، عدد الجداول/الصفوف، Cache driver/status، عدد عمليات Slow الأخيرة من السجل).
- خيار **`--json`**: إخراج التقرير كـ JSON.

### 2. Custom Metrics
- **PerformanceMonitoringService**: `recordMetric()` لتسجيل مدة العمليات، `getAverageDuration()` لمتوسط المدة، `getBaselineMetrics()` لتلخيص النظام.
- استخدام التسجيل في `CreateItemAction` و **PerformanceMonitoringMiddleware** لطلبات الويب.

### 3. Alerts للأداء البطيء
- تسجيل عمليات Slow في `storage/logs/laravel.log` (عبر `recordMetric` و DB::listen و Slow Query في الاستعلامات).
- أمر **`errors:dashboard`** لعرض آخر الأخطاء والتحذيرات من السجل.
- يمكن ربط Sentry أو Slack بالتحذيرات عند تفعيلهما.

### 4. APM (اختياري)
- **Sentry**: مُعد مسبقاً (Phase 5.2) ويدعم تتبع الأداء عند تفعيله.
- لأدوات أخرى (New Relic, Datadog): إضافة متغيرات البيئة والوكيل حسب وثائق كل أداة، ثم استخدامها في الإنتاج.

## الأوامر ذات الصلة

```bash
php artisan performance:dashboard       # لوحة الأداء
php artisan performance:dashboard --json
php artisan performance:baseline --export
php artisan performance:analyze --export
php artisan errors:dashboard
```

## الملفات المُضافة

- `app/Console/Commands/PerformanceDashboardCommand.php`

---

**التاريخ**: يناير 2026
