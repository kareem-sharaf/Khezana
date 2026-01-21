<?php

return [
    'title' => 'العروض',
    'singular' => 'عرض',
    'plural' => 'عروض',

    'fields' => [
        'operation_type' => 'نوع العملية',
        'price' => 'السعر',
        'deposit_amount' => 'مبلغ التأمين',
        'status' => 'الحالة',
        'message' => 'رسالة',
        'item' => 'الإعلان',
        'linked_item' => 'الإعلان المرتبط',
        'offer_owner' => 'صاحب العرض',
    ],

    'status' => [
        'pending' => 'قيد الانتظار',
        'accepted' => 'مقبول',
        'rejected' => 'مرفوض',
        'cancelled' => 'ملغي',
    ],

    'messages' => [
        'created_successfully' => 'تم إنشاء العرض بنجاح',
        'updated_successfully' => 'تم تحديث العرض بنجاح',
        'cancelled_successfully' => 'تم إلغاء العرض بنجاح',
        'accepted_successfully' => 'تم قبول العرض بنجاح',
        'rejected_successfully' => 'تم رفض العرض بنجاح',
        'no_offers' => 'لا توجد عروض',
    ],

    'validation' => [
        'price_required_for_sell' => 'السعر مطلوب للبيع',
        'price_required_for_rent' => 'السعر مطلوب للإيجار',
        'deposit_required_for_rent' => 'مبلغ التأمين مطلوب للإيجار',
        'request_not_approved' => 'الطلب غير معتمد',
        'request_not_open' => 'الطلب غير مفتوح',
        'request_already_fulfilled' => 'الطلب تم إتمامه بالفعل',
        'request_closed' => 'الطلب مغلق',
        'duplicate_offer' => 'لديك عرض موجود بالفعل على هذا الطلب',
        'cannot_accept_non_pending' => 'لا يمكن قبول عرض غير قيد الانتظار',
        'cannot_reject_non_pending' => 'لا يمكن رفض عرض غير قيد الانتظار',
        'cannot_update_final' => 'لا يمكن تحديث عرض نهائي (مقبول أو مرفوض)',
        'cannot_cancel_non_pending' => 'لا يمكن إلغاء عرض غير قيد الانتظار',
        'only_owner_can_cancel' => 'فقط صاحب العرض يمكنه إلغاؤه',
        'only_request_owner_can_accept' => 'فقط صاحب الطلب يمكنه قبول العرض',
        'only_request_owner_can_reject' => 'فقط صاحب الطلب يمكنه رفض العرض',
    ],

    'placeholders' => [
        'price' => 'أدخل السعر',
        'deposit_amount' => 'أدخل مبلغ التأمين',
        'message' => 'أدخل رسالة (اختياري)',
        'no_item' => 'لا يوجد إعلان مرتبط',
    ],

    'actions' => [
        'create' => 'إنشاء عرض',
        'edit' => 'تعديل العرض',
        'update' => 'تحديث',
        'cancel' => 'إلغاء العرض',
        'accept' => 'قبول',
        'reject' => 'رفض',
    ],
];
