# إصلاح مشكلة عدم ظهور الصور - Khezana

**التاريخ:** 24 يناير 2026  
**المشكلة:** الصور لا تظهر في صفحة المنتج الفردي ولا في صفحة قائمة المنتجات

---

## المشكلة

الصور لا تظهر في:
- صفحة قائمة المنتجات (`/items`)
- صفحة تفاصيل المنتج (`/items/{id}/{slug}`)

---

## الأسباب المحتملة

1. **في BrowseItemsQuery**: كان يتم جلب صورة واحدة فقط مع `limit(1)`، لكن قد لا تكون الصورة الأساسية موجودة
2. **في ItemReadModel**: كان يتم استخدام `first()` فقط بدلاً من البحث عن الصورة الأساسية أولاً
3. **في ViewItemQuery**: لم يتم جلب `path_webp` في select

---

## الإصلاحات المنفذة

### ✅ 1. إصلاح BrowseItemsQuery

**الملف:** `app/Read/Items/Queries/BrowseItemsQuery.php`

**التغييرات:**
- إزالة `limit(1)` لضمان جلب جميع الصور
- إضافة `path_webp` في select
- تحسين الترتيب: `orderBy('is_primary', 'desc')` ثم `orderBy('id', 'asc')`

**قبل:**
```php
'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary')
                       ->orderBy('is_primary', 'desc')
                       ->limit(1),
```

**بعد:**
```php
'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary', 'path_webp')
                       ->orderBy('is_primary', 'desc')
                       ->orderBy('id', 'asc'),
```

---

### ✅ 2. إصلاح ViewItemQuery

**الملف:** `app/Read/Items/Queries/ViewItemQuery.php`

**التغييرات:**
- إضافة `path_webp` في select
- تحسين الترتيب

**قبل:**
```php
'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary')
                       ->orderBy('is_primary', 'desc'),
```

**بعد:**
```php
'images' => fn($q) => $q->select('id', 'item_id', 'path', 'disk', 'is_primary', 'path_webp')
                       ->orderBy('is_primary', 'desc')
                       ->orderBy('id', 'asc'),
```

---

### ✅ 3. إصلاح ItemReadModel

**الملف:** `app/Read/Items/Models/ItemReadModel.php`

**التغييرات:**
- تحسين البحث عن الصورة الأساسية

**قبل:**
```php
$primaryImage = $images->first();
```

**بعد:**
```php
$primaryImage = $images->filter(fn($img) => $img->isPrimary)->first() 
            ?? $images->first();
```

---

### ✅ 4. إصلاح images-enhanced.blade.php

**الملف:** `resources/views/public/items/_partials/detail/images-enhanced.blade.php`

**التغييرات:**
- تغيير الشرط من `hasMultipleImages` إلى `hasImages && count > 0` لإظهار الصور المصغرة حتى لو كانت صورة واحدة

---

### ✅ 5. تحسين CSS للصور المصغرة

**الملف:** `public/css/components/image-gallery.css`

**التغييرات:**
- إضافة `display: flex !important` و `visibility: visible !important` لضمان ظهور الصور المصغرة

---

## الخطوات التالية للتحقق

1. **التحقق من قاعدة البيانات:**
   ```sql
   SELECT * FROM item_images WHERE item_id = 7;
   ```
   - تأكد من وجود سجلات للصور
   - تأكد من أن `path` غير NULL
   - تأكد من أن `is_primary` = 1 لصورة واحدة على الأقل

2. **التحقق من مسار الصور:**
   - تأكد من وجود رابط symbolic: `php artisan storage:link`
   - تأكد من وجود الملفات في `storage/app/public/items/{item_id}/`

3. **التحقق من الصلاحيات:**
   - تأكد من أن مجلد `storage/app/public` قابل للقراءة
   - تأكد من أن `public/storage` موجود ويربط إلى `storage/app/public`

---

## الاختبار

بعد الإصلاحات، يجب أن:
- ✅ تظهر الصور في صفحة قائمة المنتجات
- ✅ تظهر الصور في صفحة تفاصيل المنتج
- ✅ تظهر الصور المصغرة حتى لو كانت صورة واحدة

---

## ملاحظات

- تم إزالة `limit(1)` من BrowseItemsQuery لضمان جلب جميع الصور
- تم تحسين البحث عن الصورة الأساسية في ItemReadModel
- تم إضافة `path_webp` في جميع الاستعلامات
