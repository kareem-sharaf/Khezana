<?php

return [
    'title' => 'المنتجات',
    'singular' => 'منتج',
    'plural' => 'منتجات',

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
        'donate' => 'مشاركة',
    ],

    'messages' => [
        'created' => 'تم إنشاء المنتج بنجاح',
        'updated' => 'تم تحديث المنتج بنجاح',
        'deleted' => 'تم حذف المنتج بنجاح',
        'category_required' => 'يجب اختيار الفئة',
        'attributes_required' => 'يجب ملء جميع الخصائص المطلوبة',
    ],

    'placeholders' => [
        'title' => 'أدخل عنوان المنتج',
        'description' => 'أدخل وصف المنتج',
        'price' => 'أدخل السعر',
        'category_id' => 'اختر الفئة',
    ],
];
