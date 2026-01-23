<?php

return [
    'title' => 'الإعلانات',
    'singular' => 'إعلان',
    'plural' => 'إعلانات',

    'fields' => [
        'title' => 'العنوان',
        'description' => 'الوصف',
        'type' => 'نوع العملية',
        'price' => 'السعر',
        'category' => 'الفئة',
        'category_id' => 'الفئة',
        'user' => 'المالك',
        'status' => 'الحالة',
    ],

    'types' => [
        'sell' => 'بيع',
        'rent' => 'إيجار',
        'donate' => 'هدية',
    ],

    'messages' => [
        'created' => 'تم إنشاء الإعلان بنجاح',
        'updated' => 'تم تحديث الإعلان بنجاح',
        'deleted' => 'تم حذف الإعلان بنجاح',
        'category_required' => 'يجب اختيار الفئة',
        'attributes_required' => 'يجب ملء جميع الخصائص المطلوبة',
    ],

    'placeholders' => [
        'title' => 'أدخل عنوان الإعلان',
        'description' => 'أدخل وصف الإعلان',
        'price' => 'أدخل السعر',
        'category_id' => 'اختر الفئة',
    ],
];
