# UX Navigation Structure & User Journey
## Khezana Marketplace - Public Website

**Version:** 1.0.0  
**Platform:** Laravel Blade (SSR)  
**Target:** Syrian Market  
**User Types:** Guest (Visitor) | Authenticated User

---

## 1. Global Navigation Structure

### 1.1 Navbar - Guest User (First Visit)

**Visual Structure:**
```
┌─────────────────────────────────────────────────────────────────┐
│ [Logo]  [الإعلانات] [الطلبات] [الفئات]    [تسجيل الدخول] [إنشاء حساب] │
└─────────────────────────────────────────────────────────────────┘
```

**Left Side (Primary Navigation):**
- **Logo** (Click → Home)
- **الإعلانات** (Items) → `/items`
- **الطلبات** (Requests) → `/requests`
- **الفئات** (Categories) → Dropdown menu with categories

**Right Side (Auth Actions):**
- **تسجيل الدخول** (Login) → `/login` (Ghost Button style)
- **إنشاء حساب** (Register) → `/register` (Primary Button style)

**Visual Hierarchy:**
- Primary navigation: Visible, clear labels
- Auth buttons: Prominent on right, Register more prominent than Login
- Logo: Left-aligned, clickable to home

**Mobile View:**
- Hamburger menu (3 lines icon)
- Logo remains visible
- Menu expands to full screen overlay
- Auth buttons at bottom of mobile menu

---

### 1.2 Navbar - Authenticated User

**Visual Structure:**
```
┌─────────────────────────────────────────────────────────────────┐
│ [Logo]  [الإعلانات] [الطلبات] [الفئات]    [🔔] [👤] [إضافة إعلان] │
└─────────────────────────────────────────────────────────────────┘
```

**Left Side (Primary Navigation):**
- Same as Guest: Logo, الإعلانات, الطلبات, الفئات

**Right Side (User Actions):**
- **🔔 الإشعارات** (Notifications) → `/notifications` (Badge with count if > 0)
- **👤 حسابي** (My Account) → Dropdown menu:
  - لوحة التحكم
  - إعلاناتي
  - طلباتي
  - عروضي
  - الإعدادات
  - تسجيل الخروج
- **إضافة إعلان** (Add Item) → `/items/create` (Primary Button style)

**Visual Changes from Guest:**
- Login/Register buttons replaced with user menu
- "Add Item" button is prominent (Primary style)
- Notifications icon visible (with badge if unread)

**Mobile View:**
- Same hamburger menu
- User menu accessible from mobile menu
- "Add Item" button visible in mobile menu

---

### 1.3 Footer Navigation (Both User Types)

**Structure:**
```
┌─────────────────────────────────────────────────────────────┐
│ [عن الموقع] [الفئات] [المساعدة] [تابعنا]                      │
│                                                             │
│ [من نحن] [اتصل بنا]  [رجالي] [نسائي]  [كيفية الاستخدام] [FAQ] │
│                                                             │
│ [📘] [🐦] [📷]                                              │
│                                                             │
│ © 2024 Khezana. جميع الحقوق محفوظة.                          │
└─────────────────────────────────────────────────────────────┘
```

**Sections:**
1. **عن الموقع** (About)
   - من نحن
   - اتصل بنا
   - سياسة الخصوصية
   - شروط الاستخدام

2. **الفئات** (Categories)
   - رجالي
   - نسائي
   - أطفال
   - إكسسوارات

3. **المساعدة** (Help)
   - كيفية الاستخدام
   - الأسئلة الشائعة
   - دليل البائع
   - دليل المشتري

4. **تابعنا** (Follow Us)
   - Social media icons (Facebook, Twitter, Instagram)

---

## 2. Core User Journeys

### 2.1 Journey A: Guest Browsing Flow

#### Step 1: Landing on Homepage
**User Action:** Guest visits website for first time  
**Page:** Homepage (`/`)  
**What User Sees:**
- Hero section with search bar
- Featured categories (4-6 categories with images)
- Recent items (6-8 items in grid)
- Recent requests (4-6 requests)
- CTA: "تصفح جميع الإعلانات" (Browse All Items)

**User Options:**
1. Click on category → Go to category page
2. Click on item → Go to item detail page
3. Click "Browse All" → Go to items listing
4. Use search → Go to search results
5. Click "الطلبات" in navbar → Go to requests listing

**No Authentication Required:** ✅ All actions allowed

---

#### Step 2: Browsing Items Listing
**User Action:** Guest clicks "الإعلانات" or "Browse All"  
**Page:** Items Listing (`/items`)  
**What User Sees:**
- Page title: "جميع الإعلانات"
- Filter sidebar (left on desktop, collapsible on mobile):
  - Operation Type (Sell/Rent/Donate)
  - Category (Tree structure)
  - Price Range (Min/Max inputs)
  - Location (if implemented)
- Sort dropdown: "الأحدث", "السعر: منخفض إلى مرتفع", "السعر: مرتفع إلى منخفض"
- Items grid (3 columns desktop, 2 tablet, 1 mobile)
- Each item card shows:
  - Image
  - Operation badge (Sell/Rent/Donate)
  - Title
  - Price
  - Category
  - Date posted
- Pagination at bottom

**User Options:**
1. Click item card → Go to item detail
2. Apply filters → Results update
3. Change sort → Results update
4. Click page number → Navigate to page

**No Authentication Required:** ✅ All browsing actions allowed

---

#### Step 3: Viewing Item Detail
**User Action:** Guest clicks on item card  
**Page:** Item Detail (`/items/{id}/{slug}`)  
**What User Sees:**
- Breadcrumb: Home > Category > Item Title
- Image gallery (primary image + thumbnails)
- Item information:
  - Title (H1)
  - Operation badge (Sell/Rent/Donate)
  - Price (large, prominent)
  - Description (full text)
  - Attributes (if any)
  - Category
  - Location (if available)
  - Posted date
- Seller information:
  - Name
  - Member since date
  - Other items from seller (if any)
- Action buttons:
  - **"تواصل مع البائع"** (Contact Seller) → Requires login
  - **"إضافة إلى المفضلة"** (Add to Favorites) → Requires login
  - **"الإبلاغ عن إعلان"** (Report) → Requires login

**Guest-Specific Behavior:**
- Action buttons are visible but show tooltip on hover: "يجب تسجيل الدخول"
- Clicking action button → Redirect to login with message

**User Options:**
1. Click "Contact Seller" → Redirect to login
2. Click "Add to Favorites" → Redirect to login
3. Click seller name → View seller profile (public info only)
4. Click category → Go to category items
5. Click "Back" or breadcrumb → Return to listing

**No Authentication Required:** ✅ Viewing allowed, Actions require login

---

#### Step 4: Browsing Requests Listing
**User Action:** Guest clicks "الطلبات" in navbar  
**Page:** Requests Listing (`/requests`)  
**What User Sees:**
- Page title: "جميع الطلبات"
- Filter sidebar:
  - Status (Open/Closed)
  - Category
  - Location (if implemented)
- Sort dropdown: "الأحدث", "عدد العروض", "الحالة"
- Requests grid (same layout as items)
- Each request card shows:
  - Request badge
  - Status badge (Open/Closed)
  - Title
  - Description (truncated)
  - Category
  - Number of offers
  - Date posted

**User Options:**
1. Click request card → Go to request detail
2. Apply filters → Results update
3. Change sort → Results update

**No Authentication Required:** ✅ All browsing actions allowed

---

#### Step 5: Viewing Request Detail
**User Action:** Guest clicks on request card  
**Page:** Request Detail (`/requests/{id}/{slug}`)  
**What User Sees:**
- Breadcrumb: Home > Category > Request Title
- Request information:
  - Title (H1)
  - Status badge (Open/Closed)
  - Description (full text)
  - Attributes (if any)
  - Category
  - Location (if available)
  - Posted date
- Requester information:
  - Name
  - Member since date
- Offers section:
  - **For Guest:** "عدد العروض: X" (text only, no details)
  - **For Owner/Offerer:** Full offers list visible
- Action buttons:
  - **"تقديم عرض"** (Submit Offer) → Requires login
  - **"تواصل مع طالب"** (Contact Requester) → Requires login

**Guest-Specific Behavior:**
- Offers count visible but not details
- Action buttons show tooltip: "يجب تسجيل الدخول"
- Clicking action → Redirect to login

**User Options:**
1. Click "Submit Offer" → Redirect to login
2. Click "Contact Requester" → Redirect to login
3. Click requester name → View requester profile
4. Click category → Go to category requests

**No Authentication Required:** ✅ Viewing allowed, Actions require login

---

### 2.2 Journey B: Guest → Auth Transition

#### Scenario 1: Guest Clicks "إضافة إعلان" (Add Item)

**Step 1: Guest Action**
- Guest clicks "إضافة إعلان" button (in navbar or homepage CTA)
- **Current State:** Not authenticated

**Step 2: System Response**
- System detects: User is not authenticated
- **Action:** Redirect to `/login` with:
  - Query parameter: `redirect=/items/create`
  - Flash message: "يجب تسجيل الدخول لإضافة إعلان جديد"

**Step 3: Login Page**
- User sees login form
- Message displayed: "يجب تسجيل الدخول لإضافة إعلان جديد"
- Login form with:
  - Email/Phone input
  - Password input
  - "تسجيل الدخول" button
  - "نسيت كلمة المرور?" link
  - "ليس لديك حساب؟ إنشاء حساب" link

**Step 4: After Login**
- User submits login form
- System validates credentials
- **On Success:**
  - Redirect to `/items/create` (from `redirect` parameter)
  - Flash message: "مرحباً بك! يمكنك الآن إضافة إعلانك"
- **On Failure:**
  - Stay on login page
  - Show error: "البريد الإلكتروني أو كلمة المرور غير صحيحة"

**Step 5: Create Item Page**
- User lands on `/items/create`
- Form is ready to fill
- User can now create item

**Alternative Flow (New User):**
- If user clicks "إنشاء حساب" instead of login:
  - Register page → After registration → Redirect to `/items/create`

---

#### Scenario 2: Guest Clicks "تواصل مع البائع" (Contact Seller)

**Step 1: Guest Action**
- Guest is on item detail page (`/items/123/item-title`)
- Guest clicks "تواصل مع البائع" button
- **Current State:** Not authenticated

**Step 2: System Response**
- System detects: User is not authenticated
- **Action:** Redirect to `/login` with:
  - Query parameter: `redirect=/items/123/item-title&action=contact`
  - Flash message: "يجب تسجيل الدخول للتواصل مع البائع"

**Step 3: Login Page**
- User sees login form
- Message: "يجب تسجيل الدخول للتواصل مع البائع"
- Standard login form

**Step 4: After Login**
- User submits login form
- **On Success:**
  - Redirect to `/items/123/item-title` (original page)
  - Flash message: "مرحباً بك! يمكنك الآن التواصل مع البائع"
  - Page shows contact form/modal (now authenticated)

**Step 5: Contact Action**
- User can now see contact form
- User fills form and submits
- Message sent to seller

---

#### Scenario 3: Guest Clicks "تقديم عرض" (Submit Offer)

**Step 1: Guest Action**
- Guest is on request detail page (`/requests/456/request-title`)
- Guest clicks "تقديم عرض" button
- **Current State:** Not authenticated

**Step 2: System Response**
- System detects: User is not authenticated
- **Action:** Redirect to `/login` with:
  - Query parameter: `redirect=/requests/456/request-title&action=offer`
  - Flash message: "يجب تسجيل الدخول لتقديم عرض"

**Step 3: Login Page**
- User sees login form
- Message: "يجب تسجيل الدخول لتقديم عرض على هذا الطلب"
- Standard login form

**Step 4: After Login**
- User submits login form
- **On Success:**
  - Redirect to `/requests/456/request-title` (original page)
  - Flash message: "مرحباً بك! يمكنك الآن تقديم عرض"
  - Page shows offer form/modal (now authenticated)

**Step 5: Submit Offer**
- User can now see offer form
- User fills form (price, message, etc.) and submits
- Offer created

---

### 2.3 Journey C: Authenticated User Flow

#### Scenario: Authenticated User Adds Item

**Step 1: User Action**
- Authenticated user clicks "إضافة إعلان" in navbar
- **Current State:** Authenticated

**Step 2: System Response**
- System detects: User is authenticated
- **Action:** Direct redirect to `/items/create`
- No login required

**Step 3: Create Item Page**
- User sees create item form:
  - Title (required)
  - Description (required)
  - Category (required, dropdown)
  - Operation Type (Sell/Rent/Donate, required)
  - Price (required for Sell/Rent)
  - Deposit (required for Rent)
  - Images (upload, at least 1)
  - Attributes (dynamic based on category)
- Submit button: "نشر الإعلان"

**Step 4: Form Submission**
- User fills form and clicks "نشر الإعلان"
- System validates form
- **On Success:**
  - Item created (status: Draft)
  - Redirect to `/items/{id}/edit` or `/my-items`
  - Flash message: "تم إنشاء الإعلان بنجاح. سيتم مراجعته قبل النشر."
- **On Failure:**
  - Stay on form
  - Show validation errors

**Step 5: Item Management**
- User can:
  - Edit item
  - Submit for approval
  - Delete item
  - View item status

---

## 3. Main Actions Map

### 3.1 Actions Matrix

| Action | Requires Login? | Guest UI Behavior | Authenticated UI Behavior |
|--------|----------------|------------------|--------------------------|
| **Browse Items** | ❌ No | Full access, all items visible | Same as guest |
| **View Item Detail** | ❌ No | Full item info visible | Same as guest + can see own items even if not approved |
| **Browse Requests** | ❌ No | Full access, all requests visible | Same as guest |
| **View Request Detail** | ❌ No | Full request info visible, offers count only | Same as guest + can see own requests + offers if owner/offerer |
| **Search Items/Requests** | ❌ No | Full search functionality | Same as guest |
| **Filter Items/Requests** | ❌ No | All filters available | Same as guest |
| **Add Item** | ✅ Yes | Button visible with tooltip "يجب تسجيل الدخول" → Redirect to login | Direct access to create form |
| **Edit Item** | ✅ Yes | Not visible (only in authenticated area) | Access to edit own items |
| **Delete Item** | ✅ Yes | Not visible | Can delete own items |
| **Add Request** | ✅ Yes | Button visible with tooltip → Redirect to login | Direct access to create form |
| **Edit Request** | ✅ Yes | Not visible | Access to edit own requests |
| **Delete Request** | ✅ Yes | Not visible | Can delete own requests |
| **Submit Offer** | ✅ Yes | Button visible with tooltip → Redirect to login | Can submit offers on open requests |
| **Contact Seller** | ✅ Yes | Button visible with tooltip → Redirect to login | Can contact seller via form/message |
| **Contact Requester** | ✅ Yes | Button visible with tooltip → Redirect to login | Can contact requester |
| **Add to Favorites** | ✅ Yes | Button visible with tooltip → Redirect to login | Can add items to favorites |
| **Report Item/Request** | ✅ Yes | Link visible with tooltip → Redirect to login | Can report inappropriate content |
| **View Own Items** | ✅ Yes | Not accessible | Access to `/my-items` |
| **View Own Requests** | ✅ Yes | Not accessible | Access to `/my-requests` |
| **View Offers** | ✅ Yes | Only count visible | Full offers list if owner/offerer |
| **Accept/Reject Offer** | ✅ Yes | Not visible | Can manage offers on own requests |

---

### 3.2 Action Button States

#### Guest User - Action Buttons

**Visual State:**
- Button is visible and styled normally
- On hover: Tooltip appears: "يجب تسجيل الدخول"
- Cursor: Pointer (not disabled cursor)
- Color: Same as authenticated state

**Behavior:**
- Click → Redirect to login with redirect parameter
- No visual indication of "disabled" state
- User understands action requires login from tooltip/message

**Rationale:**
- Don't hide actions (user should know what's possible)
- Don't make buttons look disabled (confusing)
- Clear messaging about requirement

#### Authenticated User - Action Buttons

**Visual State:**
- Button is fully functional
- No tooltip needed
- Cursor: Pointer
- Color: Primary/Secondary as designed

**Behavior:**
- Click → Direct action (form, modal, etc.)
- Immediate feedback

---

## 4. Information Hierarchy

### 4.1 Homepage Priority

**Above the Fold (Immediate Visibility):**

1. **Hero Section** (Highest Priority)
   - Search bar (prominent, large)
   - Quick category buttons (4-6 categories)
   - CTA: "تصفح الإعلانات" or "تصفح الطلبات"

2. **Primary Navigation**
   - Navbar with clear links
   - Auth buttons (if guest)

3. **Featured Content** (Secondary Priority)
   - "أحدث الإعلانات" (6-8 items)
   - "أحدث الطلبات" (4-6 requests)

**Below the Fold (Scroll to See):**

4. **Categories Grid**
   - All categories with images
   - Clickable to category pages

5. **How It Works** (Optional)
   - Simple 3-step guide
   - Visual icons

6. **Footer**
   - Links, social media, copyright

**Progressive Disclosure:**
- Don't show everything at once
- Guide user attention to primary actions
- Secondary content accessible but not prominent

---

### 4.2 Items Listing Page Priority

**Above the Fold:**

1. **Page Title** + **Filter Toggle** (Mobile)
   - "جميع الإعلانات"
   - Filter button (mobile only)

2. **Active Filters** (if any applied)
   - Chips showing active filters
   - "Clear all" link

3. **Sort Dropdown**
   - Right-aligned
   - Default: "الأحدث"

4. **First Row of Items** (3-4 items visible)
   - Item cards in grid

**Below the Fold:**

5. **Filter Sidebar** (Desktop)
   - Left side, sticky
   - All filter options

6. **More Items Grid**
   - Continue scrolling

7. **Pagination**
   - Bottom of page

**Progressive Disclosure:**
- Filters hidden on mobile (collapsible)
- Show results immediately
- Filters available but not blocking

---

### 4.3 Item Detail Page Priority

**Above the Fold:**

1. **Breadcrumb**
   - Home > Category > Item Title

2. **Primary Image**
   - Large, prominent
   - Thumbnail gallery below

3. **Essential Info**
   - Title (H1)
   - Operation badge
   - Price (large, bold)
   - Status (Available/Unavailable)

4. **Primary CTA**
   - "تواصل مع البائع" (Contact Seller)
   - Most prominent button

**Below the Fold:**

5. **Description**
   - Full text
   - Expandable if long

6. **Attributes**
   - Table or list format

7. **Seller Information**
   - Name, member since
   - Other items from seller

8. **Secondary Actions**
   - Add to favorites
   - Report
   - Share (if implemented)

**Progressive Disclosure:**
- Essential info first (price, contact)
- Details available on scroll
- Actions clearly prioritized

---

### 4.4 Request Detail Page Priority

**Above the Fold:**

1. **Breadcrumb**
   - Home > Category > Request Title

2. **Request Info**
   - Title (H1)
   - Status badge (Open/Closed)
   - Description (first paragraph)

3. **Primary CTA**
   - "تقديم عرض" (Submit Offer) - if Open
   - Most prominent button

**Below the Fold:**

4. **Full Description**
   - Complete text

5. **Attributes**
   - If any

6. **Requester Information**
   - Name, member since

7. **Offers Section**
   - Count visible (for guest)
   - Full list (for owner/offerer)

**Progressive Disclosure:**
- Status and CTA first
- Details on scroll
- Offers visible based on permissions

---

## 5. UX Rules (Mandatory)

### 5.1 Authentication Rules

**Rule 1: No Hidden Requirements**
- ✅ **DO:** Show all actions, even if login required
- ❌ **DON'T:** Hide actions from guests
- **Rationale:** User should know what's possible

**Rule 2: Clear Messaging**
- ✅ **DO:** Show tooltip "يجب تسجيل الدخول" on hover
- ✅ **DO:** Show flash message after redirect: "يجب تسجيل الدخول لإتمام هذه العملية"
- ❌ **DON'T:** Surprise user after clicking
- **Rationale:** User understands requirement before action

**Rule 3: Smooth Redirect Flow**
- ✅ **DO:** Always include `redirect` parameter in login URL
- ✅ **DO:** Return user to intended page after login
- ✅ **DO:** Show success message: "مرحباً بك! يمكنك الآن..."
- ❌ **DON'T:** Redirect to generic dashboard after login
- **Rationale:** User expects to continue where they left off

**Rule 4: No Dead Ends**
- ✅ **DO:** Provide "إنشاء حساب" link on login page
- ✅ **DO:** Provide "تسجيل الدخول" link on register page
- ✅ **DO:** Show "نسيت كلمة المرور?" link
- ❌ **DON'T:** Trap user on login page
- **Rationale:** User should have options

---

### 5.2 Action Button Rules

**Rule 5: One Primary CTA Per Page**
- ✅ **DO:** Identify the most important action on each page
- ✅ **DO:** Make primary CTA most prominent (size, color, position)
- ❌ **DON'T:** Have multiple competing CTAs
- **Examples:**
  - Item Detail: "تواصل مع البائع" (primary)
  - Request Detail: "تقديم عرض" (primary)
  - Homepage: "تصفح الإعلانات" (primary)

**Rule 6: Secondary Actions Visually De-emphasized**
- ✅ **DO:** Use secondary/ghost button styles for less important actions
- ✅ **DO:** Place secondary actions below or to the side
- ❌ **DON'T:** Make all actions equal prominence
- **Examples:**
  - Item Detail: "إضافة إلى المفضلة" (secondary)
  - Item Detail: "الإبلاغ" (ghost, small)

**Rule 7: Disabled State Clarity**
- ✅ **DO:** Show tooltip explaining why action is disabled
- ✅ **DO:** Use consistent messaging: "يجب تسجيل الدخول"
- ❌ **DON'T:** Use grayed-out buttons without explanation
- **Rationale:** User should understand why they can't act

---

### 5.3 Navigation Rules

**Rule 8: Consistent Navigation**
- ✅ **DO:** Keep navbar visible on all pages (sticky)
- ✅ **DO:** Use same navigation structure throughout
- ✅ **DO:** Highlight active page in navbar
- ❌ **DON'T:** Change navigation structure per page
- **Rationale:** User should always know where they are

**Rule 9: Breadcrumb for Deep Pages**
- ✅ **DO:** Show breadcrumb on detail pages (Item/Request)
- ✅ **DO:** Make breadcrumb clickable
- ✅ **DO:** Show: Home > Category > Current Page
- ❌ **DON'T:** Show breadcrumb on listing pages (unnecessary)
- **Rationale:** User should know context and be able to navigate back

**Rule 10: Mobile Navigation Priority**
- ✅ **DO:** Hamburger menu on mobile
- ✅ **DO:** Keep logo visible
- ✅ **DO:** Show auth buttons in mobile menu
- ❌ **DON'T:** Hide important links in mobile menu
- **Rationale:** Mobile users need same access as desktop

---

### 5.4 Content Rules

**Rule 11: Progressive Disclosure**
- ✅ **DO:** Show essential info first
- ✅ **DO:** Use "Read more" for long descriptions
- ✅ **DO:** Collapse filters on mobile
- ❌ **DON'T:** Show everything at once
- **Rationale:** Reduce cognitive load, focus attention

**Rule 12: Clear Visual Hierarchy**
- ✅ **DO:** Use typography scale (H1 > H2 > H3 > Body)
- ✅ **DO:** Use spacing to group related content
- ✅ **DO:** Use color to highlight important info (price, status)
- ❌ **DON'T:** Make everything same size/weight
- **Rationale:** User should scan page easily

**Rule 13: Empty States are Helpful**
- ✅ **DO:** Show clear message: "لا توجد إعلانات"
- ✅ **DO:** Provide suggestion: "جرب تغيير الفلاتر"
- ✅ **DO:** Offer action: "إضافة إعلان جديد" (if applicable)
- ❌ **DON'T:** Show blank page
- **Rationale:** User should understand why page is empty and what to do

---

### 5.5 Performance Rules

**Rule 14: Fast Initial Load**
- ✅ **DO:** Load critical content first
- ✅ **DO:** Lazy load images below fold
- ✅ **DO:** Minimize render-blocking resources
- ❌ **DON'T:** Load everything at once
- **Rationale:** Slow internet users need fast initial render

**Rule 15: Clear Loading States**
- ✅ **DO:** Show skeleton screens or spinners during load
- ✅ **DO:** Show "جاري التحميل..." message
- ❌ **DON'T:** Show blank page while loading
- **Rationale:** User should know system is working

**Rule 16: Optimistic UI (Where Appropriate)**
- ✅ **DO:** Show immediate feedback on actions (button press)
- ✅ **DO:** Handle errors gracefully
- ❌ **DON'T:** Wait for server response before showing feedback
- **Rationale:** User feels system is responsive

---

### 5.6 Error Handling Rules

**Rule 17: Clear Error Messages**
- ✅ **DO:** Use plain language: "حدث خطأ. يرجى المحاولة لاحقاً"
- ✅ **DO:** Show specific errors for forms (field-level)
- ✅ **DO:** Provide recovery action if possible
- ❌ **DON'T:** Show technical error messages
- **Rationale:** User should understand what went wrong

**Rule 18: 404 Pages are Opportunities**
- ✅ **DO:** Show friendly message: "الصفحة غير موجودة"
- ✅ **DO:** Provide search box
- ✅ **DO:** Link to popular pages (Home, Items, Requests)
- ❌ **DON'T:** Show generic browser error
- **Rationale:** User should be able to recover from error

**Rule 19: Form Validation is Immediate**
- ✅ **DO:** Show validation errors inline
- ✅ **DO:** Highlight error fields
- ✅ **DO:** Show error summary at top of form
- ❌ **DON'T:** Wait until submit to show errors
- **Rationale:** User should fix errors as they type

---

### 5.7 Accessibility Rules

**Rule 20: Keyboard Navigation**
- ✅ **DO:** All interactive elements focusable
- ✅ **DO:** Visible focus indicators (2px outline)
- ✅ **DO:** Logical tab order
- ❌ **DON'T:** Skip focusable elements
- **Rationale:** Keyboard users need full access

**Rule 21: Screen Reader Support**
- ✅ **DO:** Use semantic HTML (header, nav, main, footer)
- ✅ **DO:** Provide ARIA labels where needed
- ✅ **DO:** Associate form labels with inputs
- ❌ **DON'T:** Rely on visual cues only
- **Rationale:** Screen reader users need context

**Rule 22: Color is Not the Only Indicator**
- ✅ **DO:** Use icons + color for status
- ✅ **DO:** Use text labels for important info
- ❌ **DON'T:** Use only color to convey meaning
- **Rationale:** Colorblind users need other indicators

---

## 6. Page-Specific UX Guidelines

### 6.1 Homepage

**Primary Goal:** Help user discover content or take primary action

**Must Have:**
- Search bar (prominent)
- Category quick links
- Recent items preview
- Clear navigation to Items/Requests

**Should Have:**
- Featured items (if any)
- How it works section (simple)

**Nice to Have:**
- Statistics (total items, users)
- Testimonials

**CTA Priority:**
1. "تصفح الإعلانات" (Primary)
2. "تصفح الطلبات" (Secondary)
3. "إضافة إعلان" (For authenticated users)

---

### 6.2 Items Listing

**Primary Goal:** Help user find relevant items quickly

**Must Have:**
- Filters (collapsible on mobile)
- Sort options
- Items grid
- Pagination

**Should Have:**
- Active filters display
- Results count
- Clear filters button

**CTA Priority:**
1. Item cards (click to view)
2. Apply filters
3. Change sort

---

### 6.3 Item Detail

**Primary Goal:** Provide all info needed to make contact decision

**Must Have:**
- Large image
- Title, price, operation type
- Description
- Contact button (primary CTA)

**Should Have:**
- Attributes
- Seller info
- Related items

**CTA Priority:**
1. "تواصل مع البائع" (Primary)
2. "إضافة إلى المفضلة" (Secondary)
3. "الإبلاغ" (Tertiary)

---

### 6.4 Requests Listing

**Primary Goal:** Help user find relevant requests quickly

**Must Have:**
- Filters (status, category)
- Sort options
- Requests grid
- Pagination

**Should Have:**
- Active filters
- Results count

**CTA Priority:**
1. Request cards (click to view)
2. Apply filters

---

### 6.5 Request Detail

**Primary Goal:** Provide all info needed to submit offer

**Must Have:**
- Title, status, description
- Submit offer button (primary CTA, if Open)

**Should Have:**
- Attributes
- Requester info
- Offers count/list (based on permissions)

**CTA Priority:**
1. "تقديم عرض" (Primary, if Open)
2. "تواصل مع طالب" (Secondary)

---

## 7. Mobile-Specific Considerations

### 7.1 Navigation

**Mobile Navbar:**
- Hamburger menu (3 lines)
- Logo always visible
- Menu expands to full screen overlay
- Auth buttons at bottom of menu

**Mobile Menu Structure:**
```
┌─────────────────────┐
│ [Logo]        [✕]   │
├─────────────────────┤
│ الإعلانات           │
│ الطلبات             │
│ الفئات              │
├─────────────────────┤
│ [تسجيل الدخول]      │
│ [إنشاء حساب]        │
└─────────────────────┘
```

### 7.2 Content Layout

**Mobile Items Grid:**
- 1 column (full width)
- Larger touch targets (min 44px × 44px)
- Simplified card layout (less info visible)

**Mobile Filters:**
- Hidden by default
- "Filter" button opens bottom sheet or modal
- Apply/Clear buttons at bottom

**Mobile Forms:**
- Full width inputs
- Larger touch targets
- Sticky submit button at bottom

---

## 8. User Flow Diagrams (Textual)

### 8.1 Guest User Journey Map

```
Entry Point (Homepage)
    │
    ├─→ Browse Items → Item Detail → Contact Seller → [Login Required]
    │
    ├─→ Browse Requests → Request Detail → Submit Offer → [Login Required]
    │
    ├─→ Search → Results → Item/Request Detail
    │
    ├─→ Category → Category Items → Item Detail
    │
    └─→ Add Item → [Login Required] → Login → Create Item
```

### 8.2 Authenticated User Journey Map

```
Entry Point (Homepage or Dashboard)
    │
    ├─→ Browse Items → Item Detail → Contact Seller → [Message Form]
    │
    ├─→ Browse Requests → Request Detail → Submit Offer → [Offer Form]
    │
    ├─→ Add Item → Create Form → Submit → Item Created
    │
    ├─→ Add Request → Create Form → Submit → Request Created
    │
    ├─→ My Items → Edit/Delete/Submit for Approval
    │
    └─→ My Requests → Edit/Delete/Close/View Offers
```

---

## 9. Success Metrics

### 9.1 Guest User Metrics

**Key Metrics:**
- Time to first item view (target: < 10 seconds)
- Bounce rate (target: < 60%)
- Pages per session (target: > 3)
- Login conversion rate (target: > 15% of guests)

**Success Indicators:**
- Guest can find items quickly
- Guest understands what requires login
- Guest successfully transitions to authenticated state

### 9.2 Authenticated User Metrics

**Key Metrics:**
- Item creation completion rate (target: > 80%)
- Request creation completion rate (target: > 80%)
- Offer submission rate (target: > 30% of request views)
- Contact action rate (target: > 20% of item views)

**Success Indicators:**
- User can complete actions without confusion
- User understands system state
- User can navigate efficiently

---

## 10. Implementation Checklist

### Phase 1: Navigation Structure
- [ ] Implement navbar component (guest state)
- [ ] Implement navbar component (authenticated state)
- [ ] Implement mobile menu
- [ ] Implement footer
- [ ] Add active page highlighting
- [ ] Add breadcrumbs to detail pages

### Phase 2: Guest Journeys
- [ ] Homepage with hero and featured content
- [ ] Items listing with filters
- [ ] Item detail page
- [ ] Requests listing with filters
- [ ] Request detail page
- [ ] Search functionality

### Phase 3: Auth Transitions
- [ ] Login redirect flow with `redirect` parameter
- [ ] Tooltip system for guest action buttons
- [ ] Flash message system
- [ ] Return to intended page after login

### Phase 4: Action Buttons
- [ ] Primary CTA identification per page
- [ ] Secondary action styling
- [ ] Guest button states with tooltips
- [ ] Authenticated button states

### Phase 5: Progressive Disclosure
- [ ] Collapsible filters on mobile
- [ ] "Read more" for long descriptions
- [ ] Lazy loading images
- [ ] Skeleton screens for loading states

### Phase 6: Error Handling
- [ ] 404 page with helpful links
- [ ] 500 error page
- [ ] Form validation with inline errors
- [ ] Clear error messages

### Phase 7: Accessibility
- [ ] Keyboard navigation
- [ ] Focus indicators
- [ ] ARIA labels
- [ ] Screen reader testing

---

**End of UX Navigation & Journey Document**

This document serves as the complete guide for implementing navigation structure and user journeys. All pages and components should follow these specifications for consistent user experience.
