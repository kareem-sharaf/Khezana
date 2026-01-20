<?php

return [
    'title' => 'العناصر',
    'singular' => 'عنصر',
    'plural' => 'عناصر',
    
    'fields' => [
        'title' => 'العنوان',
        'description' => 'الوصف',
        'operation_type' => 'نوع العملية',
        'price' => 'السعر',
        'deposit_amount' => 'مبلغ التأمين',
        'is_available' => 'متاح',
        'category' => 'الفئة',
        'category_id' => 'الفئة',
        'user' => 'المالك',
        'owner' => 'المالك',
        'status' => 'الحالة',
        'images' => 'الصور',
    ],
    
    'operation_types' => [
        'sell' => 'بيع',
        'rent' => 'إيجار',
        'donate' => 'تبرع',
    ],
    
    'availability' => [
        'available' => 'متاح',
        'unavailable' => 'غير متاح',
    ],
    
    'messages' => [
        'created' => 'تم إنشاء العنصر بنجاح',
        'updated' => 'تم تحديث العنصر بنجاح',
        'deleted' => 'تم حذف العنصر بنجاح',
        'submitted_for_approval' => 'تم إرسال العنصر للموافقة بنجاح',
        'category_required' => 'يجب اختيار الفئة',
        'attributes_required' => 'يجب ملء جميع الخصائص المطلوبة',
        'price_required_for_sell' => 'السعر مطلوب للبيع',
        'price_required_for_rent' => 'السعر مطلوب للإيجار',
        'deposit_required_for_rent' => 'مبلغ التأمين مطلوب للإيجار',
    ],
    
    'placeholders' => [
        'title' => 'أدخل عنوان العنصر',
        'description' => 'أدخل وصف العنصر',
        'price' => 'أدخل السعر',
        'deposit_amount' => 'أدخل مبلغ التأمين',
        'category_id' => 'اختر الفئة',
    ],
    
    'actions' => [
        'create' => 'إنشاء عنصر',
        'edit' => 'تعديل العنصر',
        'delete' => 'حذف العنصر',
        'submit_for_approval' => 'إرسال للموافقة',
        'view' => 'عرض العنصر',
    ],
];
