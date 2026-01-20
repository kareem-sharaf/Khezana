<?php

return [
    'title' => 'الفئات',
    'singular' => 'فئة',
    'plural' => 'فئات',
    
    'fields' => [
        'name' => 'الاسم',
        'slug' => 'الرابط',
        'description' => 'الوصف',
        'parent_id' => 'الفئة الرئيسية',
        'is_active' => 'نشط',
        'parent' => 'الفئة الرئيسية',
        'children' => 'الفئات الفرعية',
    ],
    
    'messages' => [
        'created' => 'تم إنشاء الفئة بنجاح',
        'updated' => 'تم تحديث الفئة بنجاح',
        'deleted' => 'تم حذف الفئة بنجاح',
        'cannot_delete_has_children' => 'لا يمكن حذف الفئة لأنها تحتوي على فئات فرعية',
        'cannot_be_own_parent' => 'لا يمكن أن تكون الفئة أباً لنفسها',
        'circular_reference' => 'لا يمكن تعيين هذا الأب لأنها ستنشئ مرجعاً دائرياً',
    ],
    
    'placeholders' => [
        'name' => 'أدخل اسم الفئة',
        'slug' => 'سيتم إنشاؤه تلقائياً من الاسم',
        'description' => 'أدخل وصفاً للفئة',
        'parent_id' => 'اختر الفئة الرئيسية (اختياري)',
    ],
];
