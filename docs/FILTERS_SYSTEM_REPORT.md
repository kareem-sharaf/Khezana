# تقرير شامل عن نظام الفلترة في Khezana

## جدول المحتويات
1. [نظرة عامة على نظام الفلترة](#نظرة-عامة)
2. [كيفية عمل عملية الفلترة](#كيفية-العمل)
3. [تصميم واجهة الفلاتر](#تصميم-الواجهة)
4. [سلوك الفلاتر عند التفاعل](#سلوك-التفاعل)
5. [مكونات الفلاتر التفصيلية](#مكونات-تفصيلية)
6. [السلايدر (Price Slider)](#السلايدر)
7. [الأزرار والتفاعلات](#الأزرار)

---

## نظرة عامة على نظام الفلترة

نظام الفلترة في Khezana هو نظام متكامل يسمح للمستخدمين بتصفية العناصر (Items) بناءً على معايير متعددة:
- **نوع العملية**: بيع، إيجار، تبرع
- **البحث النصي**: البحث في العنوان والوصف
- **الفئة**: اختيار فئة رئيسية أو فرعية
- **الحالة**: جديد أو مستعمل
- **نطاق السعر**: من أدنى سعر إلى أعلى سعر

### الملفات الرئيسية
- **الواجهة**: `resources/views/public/items/_partials/filters.blade.php`
- **التصميم**: `public/css/components/filters.css`
- **السلوك**: `resources/js/app.js` (Alpine.js)
- **الخلفية**: `app/Read/Items/Queries/BrowseItemsQuery.php`
- **الكنترولر**: `app/Http/Controllers/Public/ItemController.php`

---

## كيفية عمل عملية الفلترة

### 1. تدفق البيانات (Data Flow)

```
المستخدم يغير الفلتر
    ↓
النموذج (Form) يجمع القيم
    ↓
إرسال GET Request مع Query Parameters
    ↓
ItemController يستقبل الطلب
    ↓
استخراج الفلاتر من Request
    ↓
BrowseItemsQuery يطبق الفلاتر على Query
    ↓
إرجاع النتائج المفلترة
    ↓
عرض النتائج مع الفلاتر النشطة
```

### 2. معالجة الفلاتر في الخلفية

#### في `ItemController`:
```php
// استخراج الفلاتر من الطلب
$filters = [
    'operation_type' => $request->get('operation_type'),
    'category_id' => $request->get('category_id'),
    'condition' => $request->get('condition'),
    'price_min' => $request->get('price_min'),
    'price_max' => $request->get('price_max'),
    'search' => $request->get('search'),
];

// إزالة الفلاتر الفارغة
$filters = array_filter($filters, fn($value) => $value !== null && $value !== '');
```

#### في `BrowseItemsQuery`:
```php
// تطبيق كل فلتر على Query
if (isset($filters['search']) && $filters['search']) {
    $query->search($filters['search']); // Full-text search
}

if (isset($filters['operation_type']) && $filters['operation_type']) {
    $query->where('operation_type', $filters['operation_type']);
}

if (isset($filters['category_id']) && $filters['category_id']) {
    $query->where('category_id', (int) $filters['category_id']);
}

if (isset($filters['condition']) && $filters['condition']) {
    $query->where('condition', $filters['condition']);
}

if (isset($filters['price_min']) && $filters['price_min']) {
    $query->where('price', '>=', (float) $filters['price_min']);
}

if (isset($filters['price_max']) && $filters['price_max']) {
    $query->where('price', '<=', (float) $filters['price_max']);
}
```

### 3. آلية الإرسال التلقائي

**الفلاتر التي ترسل تلقائياً عند التغيير:**
- **نوع العملية** (`operation_type`): `onchange="this.form.submit()"`
- **الفئة** (`category_id`): `onchange="this.form.submit()"`
- **الحالة** (`condition`): `onchange="this.form.submit()"`
- **السعر** (`price_min`/`price_max`): إرسال تلقائي عند انتهاء السحب (`onDragEnd`)

**الفلاتر التي تحتاج ضغط "تطبيق":**
- **البحث** (`search`): يحتاج إدخال نص ثم الضغط على "تطبيق الفلاتر"

### 4. الحفاظ على معاملات URL

النظام يحافظ على جميع معاملات URL غير المتعلقة بالفلاتر:
- `sort`: طريقة الترتيب
- `per_page`: عدد العناصر في الصفحة
- أي معاملات أخرى مخصصة

```php
// في Blade Template
@foreach (request()->except(['search', 'category_id', 'condition', 'price_min', 'price_max', 'operation_type', 'page']) as $paramKey => $paramVal)
    <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
@endforeach
```

---

## تصميم واجهة الفلاتر

### 1. التخطيط العام (Layout)

#### على سطح المكتب (Desktop ≥ 1024px):
- **الموقع**: Sidebar ثابت على الجانب (sticky)
- **العرض**: 320px
- **الارتفاع**: أقصى ارتفاع `calc(100vh - 80px)`
- **الموضع**: `position: sticky` مع `top: calc(var(--khezana-spacing-xl) + 20px)`
- **الظهور**: دائماً مرئي (لا يحتاج toggle)

#### على الجوال (Mobile < 1024px):
- **الموقع**: Bottom Sheet (ورقة من الأسفل)
- **العرض**: 100% من عرض الشاشة
- **الارتفاع**: أقصى ارتفاع 85vh
- **الظهور**: مخفي افتراضياً، يظهر عند الضغط على زر الفلاتر
- **الحركة**: Animation slide-up من الأسفل
- **Overlay**: خلفية شفافة مع blur عند الفتح

### 2. هيكل المكونات

```
khezana-filters (Container)
├── khezana-filters__header (رأس الفلاتر)
│   ├── khezana-filters__title (عنوان "الفلاتر")
│   └── khezana-filters__close (زر الإغلاق - الجوال فقط)
├── khezana-filters__active-chips (الفلاتر النشطة)
│   └── khezana-active-filters__chips (Chips)
├── khezana-filters__content (محتوى الفلاتر)
│   ├── khezana-filter-group (مجموعة فلتر)
│   │   ├── khezana-filter-group__label
│   │   └── khezana-filter-group__select/input
│   └── khezana-price-slider-wrapper (السلايدر)
└── khezana-filters__actions (أزرار الإجراءات)
    ├── khezana-filters__apply (تطبيق)
    └── khezana-filters__reset (إعادة تعيين)
```

### 3. الألوان والتصميم

#### الألوان الأساسية:
- **الخلفية**: `var(--khezana-white)` (#FFFFFF)
- **الحدود**: `var(--khezana-border)` (#E5E7EB)
- **النص**: `var(--khezana-text)` (#1F2937)
- **اللون الأساسي**: `var(--khezana-primary)` (#F59E0B - برتقالي/ذهبي)
- **الخلفية الخفيفة**: `var(--khezana-bg)` (#F9FAFB)

#### الظلال والحدود:
- **Shadow**: `var(--khezana-shadow-md)` (0 4px 6px rgba(0, 0, 0, 0.1))
- **Border Radius**: `var(--khezana-radius-lg)` (12px)
- **Border**: 1px solid `var(--khezana-border)`

---

## سلوك الفلاتر عند التفاعل

### 1. عند الضغط على زر الفلاتر (Mobile)

**السلوك:**
```javascript
// Alpine.js
x-data="{ filtersOpen: false }"
@click="filtersOpen = !filtersOpen"
```

**ما يحدث:**
1. تغيير `filtersOpen` من `false` إلى `true`
2. إظهار Overlay مع fade-in animation
3. إظهار Filters Sidebar مع slide-up animation
4. إضافة class `is-active` للـ sidebar
5. إظهار زر الإغلاق (×)

**التأثيرات البصرية:**
- Overlay: `opacity: 0 → 1` (300ms)
- Sidebar: `translateY(100%) → translateY(0)` (300ms ease-out)

### 2. عند تغيير قيمة فلتر

#### أ. نوع العملية (Operation Type):
```html
<select name="operation_type" onchange="this.form.submit()">
```

**ما يحدث:**
1. المستخدم يختار قيمة من القائمة
2. `onchange` يتم تشغيله
3. النموذج يُرسل تلقائياً (`this.form.submit()`)
4. الصفحة تُعاد تحميلها مع الفلتر الجديد
5. قائمة الفلاتر النشطة (Active Filters Chips) تتحدث

#### ب. الفئة (Category):
```html
<select name="category_id" onchange="this.form.submit()">
```

**ما يحدث:**
- نفس السلوك مثل نوع العملية
- يتم البحث في الفئات الرئيسية والفرعية
- إذا كانت الفئة المختارة فرعية، يتم عرض اسمها في Chip

#### ج. الحالة (Condition):
```html
<select name="condition" onchange="this.form.submit()">
```

**ما يحدث:**
- نفس السلوك
- القيم: `new` أو `used`

#### د. السعر (Price Slider):
```javascript
onDragEnd(which) {
    // عند انتهاء السحب
    if (this.minValue !== this.lastSubmittedMin || this.maxValue !== this.lastSubmittedMax) {
        form.submit(); // إرسال تلقائي
    }
}
```

**ما يحدث:**
1. المستخدم يسحب أحد الـ thumbs (min أو max)
2. القيم تتحدث في الوقت الفعلي
3. عند رفع الإصبع (`onDragEnd`):
   - إذا تغيرت القيم، يتم إرسال النموذج تلقائياً
   - إذا لم تتغير، لا يحدث شيء

#### هـ. البحث (Search):
```html
<input type="text" name="search">
```

**ما يحدث:**
- لا إرسال تلقائي
- المستخدم يكتب ثم يضغط "تطبيق الفلاتر"
- أو يمكن استخدام Enter (إذا كان مبرمجاً)

### 3. تحديث قائمة الفلاتر النشطة (Active Filters Chips)

#### كيف يتم بناء Chips:

```php
// في Blade Template
$activeFiltersChips = [];

// فلتر البحث
if (isset($currentFilters['search']) && $currentFilters['search']) {
    $activeFiltersChips[] = [
        'key' => 'search',
        'label' => __('common.ui.search') . ': ' . $currentFilters['search'],
        'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(
            request()->except(['search', 'page']), 
            ['page' => 1]
        )),
    ];
}

// فلتر الفئة
if (isset($currentFilters['category_id']) && $currentFilters['category_id']) {
    $flatCategories = $categories->flatMap(function ($c) {
        return collect([$c])->merge($c->children ?? collect());
    });
    $category = $flatCategories->firstWhere('id', $currentFilters['category_id']);
    if ($category) {
        $activeFiltersChips[] = [
            'key' => 'category_id',
            'label' => __('items.fields.category') . ': ' . $category->name,
            'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(
                request()->except(['category_id', 'page']), 
                ['page' => 1]
            )),
        ];
    }
}

// فلتر الحالة
if (isset($currentFilters['condition']) && $currentFilters['condition']) {
    $activeFiltersChips[] = [
        'key' => 'condition',
        'label' => __('items.fields.condition') . ': ' . __('items.conditions.' . $currentFilters['condition']),
        'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(
            request()->except(['condition', 'page']), 
            ['page' => 1]
        )),
    ];
}

// فلتر السعر
if (isset($currentFilters['price_min']) || isset($currentFilters['price_max'])) {
    $priceLabel = '';
    if (isset($currentFilters['price_min']) && isset($currentFilters['price_max'])) {
        $priceLabel = number_format($currentFilters['price_min'], 0) . ' - ' . number_format($currentFilters['price_max'], 0);
    } elseif (isset($currentFilters['price_min'])) {
        $priceLabel = __('common.ui.from') . ' ' . number_format($currentFilters['price_min'], 0);
    } elseif (isset($currentFilters['price_max'])) {
        $priceLabel = __('common.ui.to') . ' ' . number_format($currentFilters['price_max'], 0);
    }
    $activeFiltersChips[] = [
        'key' => 'price',
        'label' => __('items.fields.price') . ': ' . $priceLabel,
        'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(
            request()->except(['price_min', 'price_max', 'page']), 
            ['page' => 1]
        )),
    ];
}

// فلتر نوع العملية
if (isset($currentFilters['operation_type']) && $currentFilters['operation_type']) {
    $activeFiltersChips[] = [
        'key' => 'operation_type',
        'label' => __('items.fields.operation_type') . ': ' . __('items.operation_types.' . $currentFilters['operation_type']),
        'removeUrl' => $filterRoute . '?' . http_build_query(array_merge(
            request()->except(['operation_type', 'page']), 
            ['page' => 1]
        )),
    ];
}
```

#### عند الضغط على زر الإزالة (×) في Chip:

```html
<a href="{{ $chip['removeUrl'] }}" class="khezana-filter-chip__remove">
    <span aria-hidden="true">×</span>
</a>
```

**ما يحدث:**
1. المستخدم يضغط على ×
2. الانتقال إلى `removeUrl` (GET request)
3. `removeUrl` يحتوي على جميع الفلاتر **عدا** الفلتر المحدد
4. الصفحة تُعاد تحميلها بدون هذا الفلتر
5. قائمة Chips تتحدث تلقائياً

#### عند الضغط على "مسح الكل":

```html
<a href="{{ $filterRoute . (request('operation_type') ? '?operation_type=' . request('operation_type') : '') }}"
   class="khezana-filter-chip khezana-filter-chip--clear-all">
    {{ __('common.ui.clear_all') }}
</a>
```

**ما يحدث:**
1. إزالة جميع الفلاتر **عدا** `operation_type` (إذا كان موجوداً)
2. الانتقال إلى الصفحة بدون فلاتر
3. إعادة تعيين جميع القيم إلى الافتراضي

---

## مكونات الفلاتر التفصيلية

### 1. زر الفلاتر (Toggle Button) - Mobile Only

#### الموقع:
- **Position**: `fixed`
- **Bottom**: `var(--khezana-spacing-lg)` (24px)
- **Right** (LTR) / **Left** (RTL): `var(--khezana-spacing-lg)` (24px)
- **Z-index**: 100

#### التصميم:
```css
.khezana-filters-toggle {
    background: var(--khezana-primary); /* #F59E0B */
    color: var(--khezana-white);
    border-radius: 50px; /* دائري تماماً */
    padding: var(--khezana-spacing-sm) var(--khezana-spacing-md);
    box-shadow: 0 4px 12px rgba(41, 60, 87, 0.3);
    min-width: 120px;
    display: flex;
    align-items: center;
    gap: var(--khezana-spacing-xs);
}
```

#### المحتوى:
- **أيقونة**: 🔍 (emoji)
- **نص**: "الفلاتر" أو "Filters"
- **Badge**: يظهر عدد الفلاتر النشطة (إذا كان > 0)

#### التفاعل:
- **Hover**: 
  - `background: var(--khezana-primary-dark)`
  - `transform: translateY(-2px)`
  - `box-shadow: 0 6px 16px rgba(41, 60, 87, 0.4)`
- **Active**: `transform: translateY(0)`

### 2. رأس الفلاتر (Header)

```css
.khezana-filters__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--khezana-spacing-md);
    border-bottom: 1px solid var(--khezana-border);
    background: var(--khezana-bg);
}
```

**المكونات:**
- **العنوان**: "الفلاتر" (font-size: lg, font-weight: 700)
- **زر الإغلاق**: × (يظهر فقط على الجوال)

### 3. مجموعة الفلتر (Filter Group)

```css
.khezana-filter-group {
    display: flex;
    flex-direction: column;
    gap: var(--khezana-spacing-xs);
}
```

**المكونات:**
- **Label**: نص وصفي (font-size: sm, font-weight: 600)
- **Input/Select**: حقل الإدخال

#### تصميم Input/Select:
```css
.khezana-filter-group__select,
.khezana-filter-group__input {
    width: 100%;
    padding: 0.625rem 0.75rem;
    border: 1px solid var(--khezana-border);
    border-radius: var(--khezana-radius);
    font-size: var(--khezana-font-size-sm);
    background: var(--khezana-white);
    transition: var(--khezana-transition);
}
```

**حالات التفاعل:**
- **Hover**: `border-color: var(--khezana-primary-light)`
- **Focus**: 
  - `border-color: var(--khezana-primary)`
  - `box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1)`

### 4. Chips الفلاتر النشطة (Active Filters Chips)

#### تصميم Chip عادي:
```css
.khezana-filter-chip {
    display: inline-flex;
    align-items: center;
    gap: var(--khezana-spacing-xs);
    padding: 0.375rem 0.75rem;
    background: var(--khezana-white);
    border: 1px solid var(--khezana-border);
    border-radius: var(--khezana-radius);
    font-size: var(--khezana-font-size-sm);
}
```

**المكونات:**
- **Label**: نص الفلتر (مثل "الفئة: إلكترونيات")
- **Remove Button**: × (18x18px، دائري)

#### تصميم زر الإزالة:
```css
.khezana-filter-chip__remove {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    color: var(--khezana-text-light);
    display: flex;
    align-items: center;
    justify-content: center;
}
```

**Hover على زر الإزالة:**
- `background: var(--khezana-danger)` (#EF4444)
- `color: var(--khezana-white)`

#### تصميم "مسح الكل":
```css
.khezana-filter-chip--clear-all {
    background: var(--khezana-primary);
    color: var(--khezana-white);
    border-color: var(--khezana-primary);
    font-weight: 600;
}
```

**Hover:**
- `background: var(--khezana-primary-dark)`

### 5. أزرار الإجراءات (Actions)

```css
.khezana-filters__actions {
    padding: var(--khezana-spacing-md);
    border-top: 1px solid var(--khezana-border);
    background: var(--khezana-bg);
    display: flex;
    flex-direction: column;
    gap: var(--khezana-spacing-sm);
}
```

**الأزرار:**
1. **تطبيق الفلاتر** (`khezana-filters__apply`)
   - نوع: `khezana-btn-primary`
   - لون: برتقالي/ذهبي
   - عرض: 100%

2. **إعادة تعيين** (`khezana-filters__reset`)
   - نوع: `khezana-btn-secondary`
   - لون: أبيض شفاف
   - عرض: 100%

---

## السلايدر (Price Slider)

### 1. البنية العامة

```
khezana-price-slider-wrapper
└── khezana-price-slider
    ├── khezana-price-slider__track (المسار)
    │   └── khezana-price-slider__range (النطاق المحدد)
    ├── khezana-price-slider__input--min (input للحد الأدنى)
    ├── khezana-price-slider__input--max (input للحد الأقصى)
    ├── khezana-price-slider__label--min (تسمية الحد الأدنى)
    └── khezana-price-slider__label--max (تسمية الحد الأقصى)
└── khezana-price-slider__limits (الحدود: 0 و 1,000,000)
```

### 2. التصميم البصري

#### المسار (Track):
```css
.khezana-price-slider__track {
    height: 6px;
    background: linear-gradient(to right,
        #e5e7eb 0%,
        #d1d5db 50%,
        #e5e7eb 100%);
    border-radius: 10px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}
```

#### النطاق المحدد (Range):
```css
.khezana-price-slider__range {
    height: 100%;
    background: linear-gradient(90deg,
        var(--khezana-primary) 0%,
        var(--khezana-primary-dark) 100%);
    border-radius: 10px;
    box-shadow:
        0 2px 8px rgba(245, 158, 11, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}
```

#### الـ Thumbs (المقابض):
```css
.khezana-price-slider__input::-webkit-slider-thumb {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #FFFFFF 0%, #F9FAFB 100%);
    border: 3px solid var(--khezana-primary);
    border-radius: 50%;
    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.15),
        0 0 0 4px rgba(245, 158, 11, 0.1),
        inset 0 1px 2px rgba(255, 255, 255, 0.8);
}
```

**حالات التفاعل:**
- **Hover**: 
  - `transform: scale(1.2)`
  - `box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4)`
  - `cursor: grabbing`
- **Active**: 
  - `transform: scale(1.15)`
  - `box-shadow: 0 2px 6px rgba(245, 158, 11, 0.5)`

#### التسميات (Labels):
```css
.khezana-price-slider__label {
    position: absolute;
    top: -45px;
    transform: translateX(-50%);
    z-index: 5;
}
```

**تصميم النص:**
```css
.khezana-price-slider__label-text {
    padding: 6px 12px;
    background: linear-gradient(135deg, 
        var(--khezana-primary) 0%, 
        var(--khezana-primary-dark) 100%);
    color: var(--khezana-white);
    font-size: var(--khezana-font-size-xs);
    font-weight: 700;
    border-radius: 8px;
    box-shadow:
        0 4px 12px rgba(245, 158, 11, 0.3),
        0 2px 4px rgba(0, 0, 0, 0.1);
    min-width: 60px;
    text-align: center;
}
```

**السهم (Arrow):**
```css
.khezana-price-slider__label::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid var(--khezana-primary-dark);
}
```

**عند التقارب (Stacked):**
- إذا كانت المسافة بين Min و Max < 12%، يتم تكديس التسميات
- Min: `top: -45px`
- Max: `top: -70px`

### 3. السلوك والتفاعل

#### Alpine.js Component:
```javascript
Alpine.data('priceSlider', (minValue = 0, maxValue = 1000000) => {
    return {
        minValue: initialMin,
        maxValue: initialMax,
        min: 0,
        max: 1000000,
        minGap: 500, // الحد الأدنى للفجوة بين Min و Max
        
        // حساب النسب المئوية
        get minPercent() {
            return ((this.minValue - this.min) / (this.max - this.min)) * 100;
        },
        
        get maxPercent() {
            return ((this.maxValue - this.min) / (this.max - this.min)) * 100;
        },
        
        // تحديث الحد الأدنى
        updateMin(value) {
            const newValue = Math.max(
                this.min, 
                Math.min(this.maxValue - this.minGap, parseInt(value) || this.min)
            );
            this.minValue = newValue;
        },
        
        // تحديث الحد الأقصى
        updateMax(value) {
            const newValue = Math.max(
                this.minValue + this.minGap, 
                Math.min(this.max, parseInt(value) || this.max)
            );
            this.maxValue = newValue;
        },
        
        // تنسيق السعر حسب اللغة
        formatPrice(price) {
            const locale = document.documentElement.lang || 'ar';
            return new Intl.NumberFormat(locale, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(price);
        },
        
        // بدء السحب
        onDragStart(which) {
            this.isDragging = true;
            if (which === 'min') this.isDraggingMin = true;
            if (which === 'max') this.isDraggingMax = true;
        },
        
        // انتهاء السحب
        onDragEnd(which) {
            if (which === 'min') this.isDraggingMin = false;
            if (which === 'max') this.isDraggingMax = false;
            this.isDragging = false;
            
            // إرسال تلقائي إذا تغيرت القيم
            const form = this.$el?.closest('form');
            if (form && (this.minValue !== this.lastSubmittedMin || 
                         this.maxValue !== this.lastSubmittedMax)) {
                this.lastSubmittedMin = this.minValue;
                this.lastSubmittedMax = this.maxValue;
                form.submit();
            }
        },
        
        // النقر على المسار للقفز
        onTrackClick(e) {
            const track = e.currentTarget;
            const rect = track.getBoundingClientRect();
            const percent = Math.max(0, Math.min(100, 
                ((e.clientX - rect.left) / rect.width) * 100));
            
            const step = 100;
            const raw = this.min + (this.max - this.min) * (percent / 100);
            const value = Math.round(raw / step) * step;
            const valueClamped = Math.max(this.min, Math.min(this.max, value));
            
            // تحديث الأقرب (Min أو Max)
            const distMin = Math.abs(this.minPercent - percent);
            const distMax = Math.abs(this.maxPercent - percent);
            if (distMin <= distMax) {
                this.updateMin(String(Math.min(valueClamped, this.maxValue - this.minGap)));
            } else {
                this.updateMax(String(Math.max(valueClamped, this.minValue + this.minGap)));
            }
        }
    };
});
```

#### الميزات:
1. **الحد الأدنى للفجوة**: 500 (لا يمكن أن يكون Min و Max متقاربين جداً)
2. **التحديث في الوقت الفعلي**: القيم تتحدث أثناء السحب
3. **التنسيق حسب اللغة**: استخدام `Intl.NumberFormat` مع `document.documentElement.lang`
4. **النقر على المسار**: يمكن النقر على المسار للقفز إلى قيمة
5. **تكديس التسميات**: عند التقارب، يتم تكديس التسميات عمودياً
6. **رفع z-index عند السحب**: الـ thumb الذي يُسحب يظهر فوق الآخر

### 4. الحدود (Limits)

```html
<div class="khezana-price-slider__limits">
    <span class="khezana-price-slider__limit khezana-price-slider__limit--min">0</span>
    <span class="khezana-price-slider__limit khezana-price-slider__limit--max">1,000,000</span>
</div>
```

**التصميم:**
```css
.khezana-price-slider__limit {
    font-size: var(--khezana-font-size-xs);
    color: var(--khezana-text-light);
    font-weight: 600;
    padding: 4px 8px;
    background: var(--khezana-bg);
    border-radius: 6px;
    border: 1px solid var(--khezana-border);
}
```

---

## الأزرار والتفاعلات

### 1. زر التطبيق (Apply Button)

```html
<button type="submit" class="khezana-btn khezana-btn-primary khezana-filters__apply">
    {{ __('common.ui.apply_filters') }}
</button>
```

**التصميم:**
```css
.khezana-btn-primary {
    background: var(--khezana-primary); /* #F59E0B */
    color: var(--khezana-white);
    padding: 0.625rem 1.125rem;
    border-radius: calc(var(--khezana-radius) * 0.75);
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.25);
    min-height: 44px;
}
```

**حالات التفاعل:**
- **Hover**: 
  - `background: var(--khezana-primary-dark)`
  - `transform: translateY(-1px) scale(1.02)`
  - `box-shadow: 0 4px 6px rgba(245, 158, 11, 0.25)`
  - Animation: `pulse-subtle`
- **Active**: `transform: translateY(0)`
- **Focus**: `box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3)`

**السلوك:**
- عند الضغط: إرسال النموذج (GET request)
- جميع الفلاتر تُرسل مع الطلب
- الصفحة تُعاد تحميلها مع النتائج المفلترة

### 2. زر الإعادة (Reset Button)

```html
<a href="{{ $filterRoute . (request('operation_type') ? '?operation_type=' . request('operation_type') : '') }}"
   class="khezana-btn khezana-btn-secondary khezana-filters__reset">
    {{ __('common.ui.reset_filters') }}
</a>
```

**التصميم:**
```css
.khezana-btn-secondary {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px) saturate(180%);
    color: var(--khezana-text);
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    min-height: 44px;
}
```

**حالات التفاعل:**
- **Hover**: 
  - `background: rgba(255, 255, 255, 1)`
  - `border-color: var(--khezana-primary-light)`
  - `color: var(--khezana-primary)`
  - `transform: translateY(-1px)`
- **Focus**: `box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15)`

**السلوك:**
- عند الضغط: الانتقال إلى URL بدون فلاتر (عدا `operation_type` إذا كان موجوداً)
- جميع الفلاتر تُزال
- القيم تُعاد إلى الافتراضي

### 3. زر الإغلاق (Close Button) - Mobile Only

```html
<button type="button" class="khezana-filters__close" @click="filtersOpen = false">
    <span aria-hidden="true">×</span>
</button>
```

**التصميم:**
```css
.khezana-filters__close {
    background: none;
    border: none;
    font-size: var(--khezana-font-size-2xl);
    color: var(--khezana-text-light);
    width: 32px;
    height: 32px;
    border-radius: var(--khezana-radius);
    display: flex;
    align-items: center;
    justify-content: center;
}
```

**حالات التفاعل:**
- **Hover**: 
  - `background: var(--khezana-border)`
  - `color: var(--khezana-text)`

**السلوك:**
- عند الضغط: إغلاق Filters Sidebar
- `filtersOpen = false`
- Animation: slide-down
- Overlay يختفي

---

## ملخص التصميم والسلوك

### التصميم العام:
- **النمط**: Modern, Clean, Professional
- **الألوان**: برتقالي/ذهبي (#F59E0B) كأساسي، أبيض، رمادي فاتح
- **الظلال**: ناعمة ومتدرجة
- **الحدود**: دائرية (border-radius)
- **الحركات**: سلسة (transitions و animations)

### السلوك التفاعلي:
- **إرسال تلقائي**: لمعظم الفلاتر (عدا البحث)
- **تحديث فوري**: للقيم أثناء التفاعل
- **Chips نشطة**: تظهر الفلاتر المطبقة مع إمكانية الإزالة
- **Responsive**: تصميم مختلف للجوال وسطح المكتب
- **RTL Support**: دعم كامل للغة العربية

### الأداء:
- **Caching**: استخدام Cache Service للنتائج
- **Lazy Loading**: للصور والعناصر
- **Minimal JavaScript**: فقط Alpine.js للتفاعلات الأساسية
- **Server-side Filtering**: الفلترة تتم في الخلفية

---

*تم إعداد هذا التقرير بناءً على مراجعة الكود في:*
- `resources/views/public/items/_partials/filters.blade.php`
- `public/css/components/filters.css`
- `resources/js/app.js`
- `app/Read/Items/Queries/BrowseItemsQuery.php`
- `app/Http/Controllers/Public/ItemController.php`

*تاريخ التقرير: 24 يناير 2026*
