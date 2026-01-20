# تقرير شامل عن الواجهات - منصة Khezana
## Marketplace للبيع والتأجير والتبرع وطلب الملابس

**تاريخ التقرير:** {{ date('Y-m-d') }}  
**الإصدار:** 1.0.0  
**التقنية:** Laravel Blade (SSR) + CSS Custom Properties

---

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [الهيكل العام (Layout)](#الهيكل-العام)
3. [Header / Navbar](#header--navbar)
4. [Footer](#footer)
5. [الصفحات العامة](#الصفحات-العامة)
6. [صفحات التفاصيل](#صفحات-التفاصيل)
7. [المكونات القابلة لإعادة الاستخدام](#المكونات)
8. [نظام التصميم (Design System)](#نظام-التصميم)
9. [Responsive Design](#responsive-design)
10. [SEO & Accessibility](#seo--accessibility)

---

## 🎯 نظرة عامة

منصة Khezana هي سوق إلكتروني متخصص في الملابس يدعم:
- **البيع** (Sell)
- **التأجير** (Rent)
- **التبرع** (Donate)
- **طلب الملابس** (Request)

### المميزات الرئيسية:
- ✅ واجهة عامة كاملة للزوار (بدون تسجيل)
- ✅ تصفح الإعلانات والطلبات
- ✅ بحث وتصفية متقدم
- ✅ صفحات تفاصيل كاملة مع SEO
- ✅ نظام مصادقة ذكي (Auth Guards)
- ✅ تصميم متجاوب (Mobile First)
- ✅ دعم كامل للغة العربية (RTL)

---

## 🏗️ الهيكل العام (Layout)

### ملف: `resources/views/layouts/app.blade.php`

**الوصف:**
Layout رئيسي موحد لجميع الصفحات العامة. يدعم RTL/LTR تلقائياً حسب اللغة.

**البنية:**
```
┌─────────────────────────────────────┐
│           Header (Navbar)           │
├─────────────────────────────────────┤
│                                     │
│      Flash Messages (Alerts)        │
│                                     │
│           Main Content              │
│         (@yield('content'))         │
│                                     │
├─────────────────────────────────────┤
│              Footer                  │
└─────────────────────────────────────┘
```

**المميزات:**
- دعم RTL/LTR تلقائي
- Google Fonts (Cairo للعربي، Inter للإنجليزي)
- Vite للـ assets (CSS/JS)
- Flash messages موحدة (Success, Error, Info)
- SEO Meta tags (canonical, og:tags)
- Container محدود العرض (max-width: 1200px)

**الألوان الأساسية:**
- Primary Blue: `#2563EB`
- Background: `#FFFFFF`
- Text: `#111827`
- Borders: `#E5E7EB`

---

## 🧭 Header / Navbar

### ملف: `resources/views/partials/header.blade.php`

**الوصف:**
شريط تنقل علوي ثابت (Sticky) يحتوي على:
- Logo (يسار/يمين حسب RTL)
- روابط التنقل الرئيسية
- قائمة الفئات المنسدلة
- أزرار المصادقة
- قائمة المستخدم (للمستخدمين المسجلين)

**البنية:**

#### للزوار (Guests):
```
[Logo]  [Items] [Requests] [Categories ▼]  [Login] [Register]
```

#### للمستخدمين المسجلين:
```
[Logo]  [Items] [Requests] [Categories ▼]  [Add Item] [User Menu ▼]
                                                      ├─ Dashboard
                                                      ├─ My Items
                                                      ├─ My Requests
                                                      └─ Logout
```

**المكونات:**

1. **Logo:**
   - رابط إلى الصفحة الرئيسية
   - نص: اسم التطبيق (Khezana)
   - لون: Primary Blue

2. **روابط التنقل:**
   - Items (الإعلانات)
   - Requests (الطلبات)
   - Categories (قائمة منسدلة مع subcategories)

3. **Categories Dropdown:**
   - يعرض الفئات الرئيسية
   - Submenu للفئات الفرعية
   - رابط مباشر للتصفية حسب الفئة

4. **أزرار المصادقة:**
   - للزوار: Login (Ghost Button) + Register (Primary Button)
   - للمستخدمين: Add Item (Primary Button) + User Menu

5. **User Menu:**
   - Dropdown menu
   - Dashboard, My Items, My Requests
   - Logout button

**التصميم:**
- Height: 64px
- Background: White
- Border Bottom: 1px solid #E5E7EB
- Position: Sticky top
- Z-index: 1000

**Mobile Menu:**
- Hamburger icon (3 lines)
- Menu collapsible
- Full width على mobile
- Toggle via JavaScript

---

## 🦶 Footer

### ملف: `resources/views/partials/footer.blade.php`

**الوصف:**
تذييل الصفحة يحتوي على 4 أقسام رئيسية + Copyright.

**البنية:**
```
┌─────────────────────────────────────────────────────┐
│  [About]        [Categories]    [Help]    [Follow]  │
│  - About Us      - Category 1    - How to  - Facebook│
│  - Contact Us    - Category 2    - FAQ     - Twitter │
│  - Privacy       - Category 3    - Seller  - Instagram│
│  - Terms         - Category 4    - Buyer            │
├─────────────────────────────────────────────────────┤
│  Copyright © 2024 Khezana. All rights reserved.     │
└─────────────────────────────────────────────────────┘
```

**الأقسام:**

1. **About Section:**
   - About Us
   - Contact Us
   - Privacy Policy
   - Terms of Service

2. **Categories Section:**
   - يعرض أول 4 فئات رئيسية
   - روابط مباشرة للتصفية

3. **Help Section:**
   - How to Use
   - FAQ
   - Seller Guide
   - Buyer Guide

4. **Follow Us Section:**
   - Social Media Icons (Facebook, Twitter, Instagram)
   - Emoji icons (يمكن استبدالها بـ SVG)

**التصميم:**
- Background: `#F9FAFB` (Light Gray)
- Border Top: 1px solid #E5E7EB
- Padding: 48px 24px 24px
- Grid Layout: 4 columns (Desktop), 2 (Tablet), 1 (Mobile)
- Text: Body Small, Dark Gray

---

## 📄 الصفحات العامة

### 1. صفحة قائمة الإعلانات (Items Index)

**الملف:** `resources/views/public/items/index.blade.php`  
**الرابط:** `/items`

**الوصف:**
صفحة عرض جميع الإعلانات المعتمدة والمتاحة مع إمكانيات البحث والتصفية والترتيب.

**البنية:**
```
┌─────────────────────────────────────────────────────┐
│  [Items]                    [Search Bar]  [Sort ▼] │
├──────────────┬──────────────────────────────────────┤
│              │  [Active Filters Chips]              │
│  [Filters]   │  ┌────────────────────────────────┐ │
│  - Operation │  │ [Item Card] [Item Card] [Item] │ │
│  - Category  │  │ [Item Card] [Item Card] [Item] │ │
│  - Price     │  │ [Item Card] [Item Card] [Item] │ │
│              │  └────────────────────────────────┘ │
│              │  [Pagination]                        │
└──────────────┴──────────────────────────────────────┘
```

**المكونات:**

1. **Header Section:**
   - Title: "Items" (أو "الإعلانات")
   - Search Bar (max-width: 400px)
   - Sort Dropdown:
     - Latest (الأحدث)
     - Price: Low to High
     - Price: High to Low
     - Title: A-Z / Z-A

2. **Sidebar (Filters):**
   - Operation Type (Sell, Rent, Donate)
   - Category (Dropdown)
   - Price Range (Min - Max inputs)
   - Apply Filters button
   - Clear Filters link

3. **Active Filters Display:**
   - Chips لكل filter نشط
   - زر × لإزالة كل filter
   - Clear All link

4. **Items Grid:**
   - Grid layout: 3-4 columns (Desktop), 2 (Tablet), 1 (Mobile)
   - Item Cards (انظر Item Card Component)
   - Empty State إذا لم توجد نتائج

5. **Pagination:**
   - Server-side pagination
   - Previous/Next buttons
   - Page numbers
   - Showing X to Y of Z results

**التصميم:**
- Layout: Grid (Sidebar 250px + Main flexible)
- Sidebar: Sticky top (80px)
- Grid Gap: 24px
- Card spacing: 16px

**Mobile:**
- Sidebar collapsible
- Filters toggle button
- Single column grid

---

### 2. صفحة تفاصيل الإعلان (Item Show)

**الملف:** `resources/views/public/items/show.blade.php`  
**الرابط:** `/items/{id}/{slug}`

**الوصف:**
صفحة تفاصيل كاملة لإعلان واحد مع جميع المعلومات والصور والإجراءات.

**البنية:**
```
┌─────────────────────────────────────────────────────┐
│  [Home > Category > Item Title] (Breadcrumbs)       │
├─────────────────────────────────────────────────────┤
│  [Item Title]                                        │
│  [Badge: Sell] [Badge: Available] [Category]       │
│                                                      │
│  ┌──────────────────┐                               │
│  │                  │                               │
│  │   Main Image     │  [Price: 500 SAR]            │
│  │                  │  [Deposit: 100 SAR] (if rent)│
│  │                  │                               │
│  └──────────────────┘                               │
│  [Thumbnail] [Thumbnail] [Thumbnail]                │
│                                                      │
│  [Description]                                       │
│  Lorem ipsum dolor sit amet...                       │
│                                                      │
│  [Attributes]                                        │
│  - Size: Large                                       │
│  - Color: Blue                                       │
│  - Material: Cotton                                  │
│                                                      │
│  [Seller Info]                                       │
│  Name: John Doe                                      │
│  Member since: Jan 2024                             │
│                                                      │
│  Published: 2 days ago                              │
│                                                      │
│  [Contact Seller] [Add to Favorites] [Report]       │
└─────────────────────────────────────────────────────┘
```

**المكونات:**

1. **Breadcrumbs:**
   - Home > Category > Item Title
   - روابط قابلة للنقر

2. **Title & Meta:**
   - H1: Item Title
   - Badges: Operation Type, Availability Status
   - Category name

3. **Image Gallery:**
   - Main image (large, aspect ratio maintained)
   - Thumbnails (إذا كانت هناك صور متعددة)
   - Click على thumbnail لتغيير الصورة الرئيسية
   - Placeholder إذا لم توجد صور

4. **Price Section:**
   - Price (Bold, Primary Blue, 24px)
   - Deposit Amount (إذا كان تأجير)

5. **Description:**
   - Full text description
   - Line breaks preserved (nl2br)

6. **Attributes List:**
   - Definition list (dl/dt/dd)
   - Attribute name: value format

7. **Seller Information:**
   - Seller name
   - Member since date

8. **Published Date:**
   - Formatted date

9. **Action Buttons:**
   - **للزوار:**
     - Contact Seller (مع tooltip: "يجب تسجيل الدخول")
     - Add to Favorites (مع tooltip)
     - Report (مع tooltip)
   - **للمستخدمين المسجلين:**
     - نفس الأزرار بدون tooltips
     - لا تظهر إذا كان المستخدم هو صاحب الإعلان

**SEO Features:**
- Schema.org Product markup
- Open Graph tags
- Canonical URL
- Meta robots

---

### 3. صفحة قائمة الطلبات (Requests Index)

**الملف:** `resources/views/public/requests/index.blade.php`  
**الرابط:** `/requests`

**الوصف:**
صفحة عرض جميع الطلبات المعتمدة والمفتوحة مع إمكانيات البحث والتصفية.

**البنية:**
مشابهة لصفحة Items Index لكن:
- Filters مختلفة (Status, Category فقط)
- Sort options مختلفة (Status, Title)
- Request Cards بدلاً من Item Cards

**المكونات:**

1. **Header:**
   - Title: "Requests"
   - Search Bar
   - Sort Dropdown

2. **Sidebar Filters:**
   - Status (Open, Closed)
   - Category

3. **Requests Grid:**
   - Request Cards (انظر Request Card Component)
   - Empty State

4. **Pagination**

---

### 4. صفحة تفاصيل الطلب (Request Show)

**الملف:** `resources/views/public/requests/show.blade.php`  
**الرابط:** `/requests/{id}/{slug}`

**الوصف:**
صفحة تفاصيل كاملة لطلب واحد مع العروض المقدمة.

**البنية:**
```
┌─────────────────────────────────────────────────────┐
│  [Home > Category > Request Title]                  │
├─────────────────────────────────────────────────────┤
│  [Request Title]                                    │
│  [Badge: Open] [Category]                          │
│                                                      │
│  [Description]                                       │
│                                                      │
│  [Attributes]                                        │
│                                                      │
│  [Offers Section] (if exists)                       │
│  ┌──────────────────────────────────────────────┐ │
│  │ Offer 1: [Badge] [Price] [Message] [Status]  │ │
│  │ Offer 2: [Badge] [Price] [Message] [Status]  │ │
│  └──────────────────────────────────────────────┘ │
│                                                      │
│  [Requester Info]                                   │
│  [Published Date]                                    │
│                                                      │
│  [Submit Offer] [Contact] [Report]                  │
└─────────────────────────────────────────────────────┘
```

**المكونات الخاصة:**

1. **Offers Section:**
   - يعرض جميع العروض المقدمة
   - كل عرض يحتوي على:
     - Offer author
     - Operation type badge
     - Price (إن وجد)
     - Message
     - Status badge
     - Linked item (إن وجد)

2. **Action Buttons:**
   - Submit Offer (فقط للطلبات المفتوحة)
   - Contact Requester
   - Report

---

## 🧩 المكونات القابلة لإعادة الاستخدام

### 1. Button Component
**الملف:** `resources/views/components/button.blade.php`

**الأنواع:**
- `primary`: أزرق (#2563EB)، نص أبيض
- `secondary`: شفاف، حدود زرقاء
- `ghost`: شفاف، نص رمادي

**الأحجام:**
- `small`: 6px 12px, 12px font
- `medium`: 8px 16px, 14px font (default)
- `large`: 12px 24px, 16px font

**الاستخدام:**
```blade
<x-button type="primary" size="medium">Click Me</x-button>
<x-button type="secondary" href="/link">Link Button</x-button>
```

---

### 2. Badge Component
**الملف:** `resources/views/components/shared/badge.blade.php`

**الأنواع:**
- Operation: `sell`, `rent`, `donate`, `request`
- Status: `available`, `pending`, `closed`, `open`, `approved`, `rejected`

**الألوان:**
- Sell: Blue (#2563EB)
- Rent: Teal (#0D9488)
- Donate: Green (#059669)
- Request: Purple (#7C3AED)
- Available/Open: Green (#10B981)
- Pending: Orange (#F59E0B)
- Closed/Rejected: Red (#EF4444)

**الاستخدام:**
```blade
<x-shared.badge type="sell" label="Sell" />
<x-shared.badge type="request">Request</x-shared.badge>
```

---

### 3. Item Card Component
**الملف:** `resources/views/components/items/item-card.blade.php`

**المحتوى:**
- Image (16:9 aspect ratio)
- Operation Type Badge
- Title (H3, 2 lines max)
- Price (Bold, Primary Blue)
- Category
- Date (diffForHumans)
- Author name

**التصميم:**
- White background
- Border: 1px solid #E5E7EB
- Border radius: 8px
- Hover: Border color changes to Primary Blue
- Box shadow on hover

---

### 4. Request Card Component
**الملف:** `resources/views/components/requests/request-card.blade.php`

**المحتوى:**
- Request Badge + Status Badge
- Title (H3)
- Description (2 lines max, 120 chars)
- Category
- Offers Count
- Date
- Author name

**التصميم:**
- مشابه لـ Item Card
- بدون صورة

---

### 5. Alert Component
**الملف:** `resources/views/components/alert.blade.php`

**الأنواع:**
- `success`: Green background
- `error`: Red background
- `warning`: Orange background
- `info`: Blue background

**المميزات:**
- Dismissible option
- Border left accent (4px)
- Icon support (optional)

---

### 6. Search Bar Component
**الملف:** `resources/views/components/search-bar.blade.php`

**المميزات:**
- Input field مع search icon
- Placeholder customizable
- Action URL customizable
- Auto-submit on Enter

---

### 7. Filters Components
**الملفات:**
- `resources/views/components/filters/items-filters.blade.php`
- `resources/views/components/filters/requests-filters.blade.php`

**المحتوى:**
- Form مع GET method
- Select dropdowns
- Price range inputs (Items only)
- Apply button
- Clear filters link

---

### 8. Breadcrumbs Component
**الملف:** `resources/views/components/breadcrumbs.blade.php`

**الاستخدام:**
```blade
<x-breadcrumbs :items="[
    ['label' => 'Home', 'url' => route('home')],
    ['label' => 'Category', 'url' => route('...')],
    ['label' => 'Current Page', 'url' => '...'],
]" />
```

---

### 9. Pagination Component
**الملف:** `resources/views/components/shared/pagination.blade.php`

**المميزات:**
- Previous/Next buttons
- Page numbers
- Active page highlight
- Disabled states
- Showing X to Y of Z results

---

### 10. Empty State Component
**الملف:** `resources/views/components/shared/empty-state.blade.php`

**المحتوى:**
- Icon (optional, emoji أو SVG)
- Title (H3)
- Description
- CTA Button (optional)

---

### 11. Form Components
**الملفات:**
- `resources/views/components/form/input.blade.php`
- `resources/views/components/form/textarea.blade.php`
- `resources/views/components/form/select.blade.php`

**المميزات:**
- Label مع required indicator
- Error messages
- Focus states
- Disabled states

---

## 🎨 نظام التصميم (Design System)

### الألوان

#### Primary Colors:
- **Primary Blue:** `#2563EB`
  - Usage: Main CTAs, links, active states
- **Primary Dark:** `#1E40AF`
  - Usage: Hover states

#### Secondary Colors:
- **Teal:** `#0D9488` (Rent)
- **Green:** `#059669` (Donate)

#### Neutral Colors:
- **White:** `#FFFFFF`
- **Light Gray:** `#F9FAFB` (Backgrounds)
- **Medium Gray:** `#E5E7EB` (Borders)
- **Dark Gray:** `#6B7280` (Secondary text)
- **Black:** `#111827` (Primary text)

#### Semantic Colors:
- **Success:** `#10B981`
- **Warning:** `#F59E0B`
- **Error:** `#EF4444`
- **Info:** `#3B82F6`

### Typography

#### Fonts:
- **Arabic:** Cairo (400, 600, 700)
- **English:** Inter (400, 600, 700)

#### Type Scale:
- **H1:** 32px (2rem), Bold
- **H2:** 24px (1.5rem), Bold
- **H3:** 20px (1.25rem), Semi-Bold
- **Body:** 14px (0.875rem), Regular
- **Small:** 12px (0.75rem), Regular
- **Caption:** 11px (0.6875rem), Regular

### Spacing System

**Base Unit:** 8px

- `xs`: 4px
- `sm`: 8px
- `md`: 16px
- `lg`: 24px
- `xl`: 32px
- `2xl`: 48px
- `3xl`: 64px

### Border Radius

- `sm`: 4px
- `md`: 6px
- `lg`: 8px

---

## 📱 Responsive Design

### Breakpoints:

1. **Mobile:** < 640px
   - Single column layout
   - Collapsible sidebar
   - Hamburger menu
   - Full-width buttons

2. **Tablet:** 640px - 1024px
   - 2 columns grid
   - Sidebar collapsible
   - Footer: 2 columns

3. **Desktop:** > 1024px
   - 3-4 columns grid
   - Fixed sidebar
   - Footer: 4 columns
   - Max-width: 1200px

### Mobile Features:
- Hamburger menu
- Filters toggle button
- Stacked layout
- Touch-friendly buttons (min 44px)

---

## 🔍 SEO & Accessibility

### SEO Features:

1. **Meta Tags:**
   - Title (dynamic per page)
   - Description
   - Open Graph tags
   - Canonical URLs

2. **Structured Data:**
   - Schema.org Product (Items)
   - Schema.org Article (Requests)
   - Person schema (Sellers)

3. **URLs:**
   - SEO-friendly slugs
   - 301 redirects for incorrect slugs

### Accessibility Features:

1. **ARIA Labels:**
   - Navigation landmarks
   - Button labels
   - Form labels

2. **Semantic HTML:**
   - Proper heading hierarchy
   - Article, Section, Nav elements
   - Time elements with datetime

3. **Keyboard Navigation:**
   - Focus states
   - Tab order
   - Skip links

4. **Screen Readers:**
   - Alt text for images
   - Descriptive link text
   - Form error messages

---

## 📊 إحصائيات الواجهات

### الصفحات العامة:
- ✅ Items Index (`/items`)
- ✅ Item Show (`/items/{id}/{slug}`)
- ✅ Requests Index (`/requests`)
- ✅ Request Show (`/requests/{id}/{slug}`)

### المكونات:
- ✅ 25+ Reusable Components
- ✅ Form Components (Input, Textarea, Select)
- ✅ UI Components (Button, Badge, Alert, Card)
- ✅ Layout Components (Container, Section, Breadcrumbs)
- ✅ Feature Components (Search, Filters, Pagination)

### التصميم:
- ✅ Design System كامل
- ✅ CSS Variables
- ✅ Responsive (Mobile First)
- ✅ RTL Support
- ✅ Dark mode ready (يمكن إضافته)

---

## 🎯 الحالة الحالية

### ✅ مكتمل:
- Layout الرئيسي
- Header مع Navigation
- Footer كامل
- صفحات Items (Index + Show)
- صفحات Requests (Index + Show)
- جميع المكونات الأساسية
- نظام التصميم
- Responsive Design
- SEO Features
- Accessibility Features

### 🔄 يمكن تحسينه:
- إضافة صور حقيقية (حالياً placeholder)
- تحسين Image Gallery (lightbox)
- إضافة Loading states
- تحسين Empty States
- إضافة Skeleton loaders

---

## 📝 ملاحظات تقنية

### الأداء:
- CSS واحد فقط (app.css)
- No JavaScript frameworks
- Lazy loading للصور
- Server-side rendering
- Caching على مستوى HTTP و Read Models

### التوافق:
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers
- RTL support (Arabic)
- Screen readers

---

**نهاية التقرير**
