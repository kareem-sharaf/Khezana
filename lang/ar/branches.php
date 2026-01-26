<?php

return [
    'title' => 'الفروع',
    'singular' => 'فرع',
    'plural' => 'فروع',

    'sections' => [
        'basic_info' => 'المعلومات الأساسية',
        'contact_info' => 'معلومات التواصل',
        'location' => 'الموقع الجغرافي',
        'working_hours' => 'ساعات العمل',
    ],

    'fields' => [
        'name' => 'اسم الفرع',
        'code' => 'كود الفرع',
        'address' => 'العنوان',
        'city' => 'المدينة',
        'latitude' => 'خط العرض',
        'longitude' => 'خط الطول',
        'phone' => 'رقم الهاتف',
        'email' => 'البريد الإلكتروني',
        'working_hours' => 'ساعات العمل',
        'is_active' => 'نشط',
        'admins_count' => 'عدد الموظفين',
        'day' => 'اليوم',
        'hours' => 'الساعات',
    ],

    'placeholders' => [
        'name' => 'أدخل اسم الفرع',
        'code' => 'مثال: DMQ',
        'address' => 'أدخل عنوان الفرع',
        'city' => 'اختر المدينة',
        'latitude' => 'مثال: 24.7136',
        'longitude' => 'مثال: 46.6753',
        'phone' => 'أدخل رقم الهاتف',
        'email' => 'أدخل البريد الإلكتروني',
        'select_branch' => 'اختر الفرع',
    ],

    'hints' => [
        'code' => 'كود مختصر وفريد للفرع (2-10 أحرف)',
        'user_branch' => 'الفرع الذي يتبع له هذا المستخدم',
    ],

    'filters' => [
        'active_only' => 'النشطة فقط',
        'inactive_only' => 'غير النشطة فقط',
    ],

    'actions' => [
        'create' => 'إنشاء فرع',
        'edit' => 'تعديل الفرع',
        'delete' => 'حذف الفرع',
        'view' => 'عرض الفرع',
        'add_day' => 'إضافة يوم',
    ],

    'messages' => [
        'created_successfully' => 'تم إنشاء الفرع بنجاح',
        'updated_successfully' => 'تم تحديث الفرع بنجاح',
        'deleted_successfully' => 'تم حذف الفرع بنجاح',
        'cannot_delete_with_users' => 'لا يمكن حذف فرع يحتوي على موظفين',
    ],
];
