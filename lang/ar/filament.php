<?php

return [
    // Navigation Groups
    'navigation_groups' => [
        'content_management' => 'إدارة المحتوى',
        'user_management' => 'إدارة المستخدمين',
        'moderation' => 'المراقبة والموافقة',
        'settings' => 'الإعدادات',
        'administration' => 'الإدارة',
    ],

    // Common Actions
    'actions' => [
        'create' => 'إنشاء',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'view' => 'عرض',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'back' => 'رجوع',
        'search' => 'بحث',
        'filter' => 'تصفية',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'archive' => 'أرشفة',
    ],

    // Common Table Columns
    'table' => [
        'id' => 'المعرف',
        'name' => 'الاسم',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'status' => 'الحالة',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'type' => 'النوع',
        'price' => 'السعر',
        'owner' => 'المالك',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
    ],

    // Common Status
    'status' => [
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'suspended' => 'معلق',
    ],

    // Common Filters
    'filters' => [
        'active_only' => 'نشط فقط',
        'inactive_only' => 'غير نشط فقط',
        'all' => 'الكل',
    ],

    // Common Sections
    'sections' => [
        'user_information' => 'معلومات المستخدم',
        'roles_permissions' => 'الأدوار والصلاحيات',
        'profile_information' => 'معلومات الملف الشخصي',
        'approval_information' => 'معلومات الموافقة',
        'content_information' => 'معلومات المحتوى',
        'rejection_reason' => 'سبب الرفض',
    ],

    // Common Messages
    'messages' => [
        'operation_completed' => 'تمت العملية بنجاح',
        'error_occurred' => 'حدث خطأ',
        'not_found' => 'غير موجود',
        'unauthorized' => 'غير مصرح',
        'deleted_successfully' => 'تم الحذف بنجاح',
        'saved_successfully' => 'تم الحفظ بنجاح',
        'updated_successfully' => 'تم التحديث بنجاح',
        'created_successfully' => 'تم الإنشاء بنجاح',
    ],

    // Common Confirmations
    'confirmations' => [
        'delete' => 'هل أنت متأكد من الحذف؟',
        'deactivate' => 'هل أنت متأكد من التعطيل؟',
        'approve' => 'هل أنت متأكد من الموافقة؟',
        'reject' => 'هل أنت متأكد من الرفض؟',
    ],
];
