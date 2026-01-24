# Phase 2.1: Full-Text Search

## ✅ ما تم إنجازه

### 1. استخدام MySQL Full-Text بدلاً من LIKE

**الخيار**: MySQL Full-Text Search (بدون Meilisearch/Scout) للحفاظ على البساطة.

**الملفات المُحدّثة**:

1. **`app/Models/Item.php`**
   - إضافة `scopeSearch($query, string $term)`:
     - يستخدم `whereFullText(['title', 'description'], $term)` عند توفر الفهرس.
     - يتراجع إلى `LIKE` عند فشل Full-Text (مثلاً قبل تشغيل الـ migration).

2. **`app/Read/Items/Queries/BrowseItemsQuery.php`**
   - استبدال شرط البحث بـ `$query->search($filters['search'])`.

3. **`app/Http/Controllers/ItemController.php`** (قائمة منتجات المستخدم)
   - استبدال شرط البحث بـ `$query->search($request->get('search'))`.

### 2. الاعتماد على Migration Phase 1.1

- Migration `2026_01_24_000001_add_phase1_performance_indexes_to_items_table` يضيف:
  - `FULLTEXT idx_items_fulltext_search (title, description)` إذا دعمت MySQL ذلك.

### 3. آلية Fallback

- عند فشل Full-Text (مثلاً عدم وجود الفهرس):
  - يتم تسجيل `Log::debug('Full-text search failed, using LIKE', ...)`.
  - يُستخدم البحث بـ `LIKE` كما سابقاً.

## 📊 الفرق بين Full-Text و LIKE

| الجانب        | LIKE                    | Full-Text                    |
|---------------|-------------------------|------------------------------|
| الأداء        | أبطأ مع بيانات كثيرة    | أسرع بفضل الفهرس             |
| التطابق       | نمط نصي ثابت            | تطابق دلالي (كلمات، جذور)   |
| الاستعلام     | `title LIKE '%x%'`      | `MATCH(title,description) AGAINST('x')` |

## 🚀 تشغيل التحسين

1. تشغيل الـ migration (يتطلب Redis/Cache متاحاً حسب إعداد المشروع):
   ```bash
   php artisan migrate --force
   ```
2. التأكد من وجود الفهرس:
   ```sql
   SHOW INDEX FROM items WHERE Key_name = 'idx_items_fulltext_search';
   ```

## 📋 ملاحظات

- **جدول الطلبات (requests)**: لا يزال البحث فيه بـ `LIKE` (لا fulltext index على هذا الجدول).
- **UserRepository**: البحث بـ LIKE على `name`، `email`، `phone` — لا يتأثر.

---

**التاريخ**: يناير 2026  
**الحالة**: ✅ مكتمل
