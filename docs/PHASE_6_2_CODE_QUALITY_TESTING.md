# Phase 6.2: Code Quality و Testing

## ما تم إنجازه

### 1. PHPStan + Larastan
- تثبيت `phpstan/phpstan`, `larastan/larastan`, `phpstan/extension-installer`.
- إعداد `phpstan.neon.dist`: level 5، مسارات `app`، وإهمال أخطاء محددة إن وجدت.

### 2. Laravel Pint (موجود مسبقاً)
- استخدام `./vendor/bin/pint` لتنظيم الكود.
- إضافة سكربت `composer pint` و `composer qa` (pint + phpstan + test).

### 3. Unit Tests
- **ItemServiceTest**: قواعد العمليات (sell/rent/donate)، اشتراط السعر والتأمين.
- **CacheServiceTest**: تنسيق مفاتيح الكاش (`getItemsIndexKey`, `getItemShowKey`)، و`invalidateItem` لا يرمي.
- **ImageOptimizationServiceTest**: رفض MIME غير صالح، رفض الحجم الزائد، قبول JPEG صالح، و`getUrl`. تخطّي اختبارات الصور عند غياب GD.

### 4. Feature Tests
- **ItemCreationTest**: اشتراط المصادقة، التحقق من الحقول المطلوبة، إنشاء عنصر ببيانات صالحة.
- **ItemSearchTest**: صفحة الفهرس تُرجع 200، والبحث بـ `?search=test` يعمل.

### 5. CI Pipeline (GitHub Actions)
- Workflow `.github/workflows/ci.yml`: تشغيل على push/PR إلى `main` أو `develop`.
- خطوات: Pint، PHPStan، ثم `php artisan test`.

### 6. تصحيح scopeSearch لـ SQLite
- استخدام Full-Text على MySQL فقط؛ على SQLite وغيره استخدام `LIKE` حتى تعمل الاختبارات مع `:memory:`.

## الأوامر

```bash
composer pint        # تنظيم الكود
composer phpstan     # تحليل ثابت
composer test        # تشغيل الاختبارات
composer qa          # pint + phpstan + test
```

## الملفات المُضافة/المُعدَّلة

- `phpstan.neon.dist`
- `composer.json` (سكربتات pint, phpstan, qa)
- `tests/Unit/ItemServiceTest.php`, `CacheServiceTest.php`, `ImageOptimizationServiceTest.php`
- `tests/Feature/ItemCreationTest.php`, `ItemSearchTest.php`
- `.github/workflows/ci.yml`
- `app/Models/Item.php` (scopeSearch: استخدام LIKE على SQLite)
- `phpunit.xml` (تغطية: `coverage` + `report`)

## ملاحظة

- `ExampleTest` و `ProfileTest` قد يفشلان بسبب سلوك التطبيق الحالي (مثلاً الصفحة الرئيسية، توجيه البروفايل). اختبارات Phase 6.2 الجديدة تعمل بشكل صحيح.

---

**التاريخ**: يناير 2026
