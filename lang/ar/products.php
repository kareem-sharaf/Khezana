<?php

return [
    'title' => 'الملابس',
    'singular' => 'لباس',
    'plural' => 'ملابس',

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
        'created' => 'تم إنشاء اللباس بنجاح',
        'updated' => 'تم تحديث اللباس بنجاح',
        'deleted' => 'تم حذف اللباس بنجاح',
        'category_required' => 'يجب اختيار الفئة',
        'attributes_required' => 'يجب ملء جميع الخصائص المطلوبة',
    ],

    'placeholders' => [
        'title' => 'أدخل عنوان اللباس',
        'description' => 'أدخل وصف اللباس',
        'price' => 'أدخل السعر',
        'category_id' => 'اختر الفئة',
    ],
];
