<?php

return [
    'resources' => [
        'categories' => [
            'label' => 'الفئات',
            'plural_label' => 'الفئات',
            'navigation_label' => 'الفئات',
            'navigation_group' => 'إدارة المحتوى',
        ],
        'attributes' => [
            'label' => 'الخاصية',
            'plural_label' => 'الخصائص',
            'navigation_label' => 'الخصائص',
            'navigation_group' => 'إدارة المحتوى',
        ],
        'products' => [
            'label' => 'المنتج',
            'plural_label' => 'المنتجات',
            'navigation_label' => 'المنتجات',
            'navigation_group' => 'إدارة المحتوى',
        ],
        'users' => [
            'label' => 'المستخدم',
            'plural_label' => 'المستخدمين',
            'navigation_label' => 'المستخدمين',
            'navigation_group' => 'إدارة المستخدمين',
        ],
        'approvals' => [
            'label' => 'الموافقة',
            'plural_label' => 'الموافقات',
            'navigation_label' => 'الموافقات',
            'navigation_group' => 'إدارة المحتوى',
        ],
        'roles' => [
            'label' => 'الدور',
            'plural_label' => 'الأدوار',
            'navigation_label' => 'الأدوار',
            'navigation_group' => 'إدارة المستخدمين',
        ],
        'permissions' => [
            'label' => 'الصلاحية',
            'plural_label' => 'الصلاحيات',
            'navigation_label' => 'الصلاحيات',
            'navigation_group' => 'إدارة المستخدمين',
        ],
    ],
    
    'actions' => [
        'create' => 'إنشاء',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'view' => 'عرض',
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'archive' => 'أرشفة',
    ],
    
    'table' => [
        'columns' => [
            'id' => 'المعرف',
            'name' => 'الاسم',
            'title' => 'العنوان',
            'description' => 'الوصف',
            'status' => 'الحالة',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التحديث',
        ],
        'filters' => [
            'active' => 'نشط فقط',
            'inactive' => 'غير نشط فقط',
            'all' => 'الكل',
        ],
    ],
];
