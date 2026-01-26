<?php

return [
    'title' => 'الموافقات',
    'singular' => 'موافقة',
    'plural' => 'موافقات',
    
    'fields' => [
        'status' => 'الحالة',
        'approvable_type' => 'نوع المحتوى',
        'approvable_title' => 'عنوان المحتوى',
        'submitted_by' => 'تم الإرسال بواسطة',
        'reviewed_by' => 'تمت المراجعة بواسطة',
        'reviewed_at' => 'تاريخ المراجعة',
        'submitted_at' => 'تاريخ الإرسال',
        'rejection_reason' => 'سبب الرفض',
        'verification_message' => 'رسالة للمستخدم',
    ],
    
    'statuses' => [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'archived' => 'مؤرشف',
        'verification_required' => 'بانتظار التحقق في الفرع',
    ],
    
    // Legacy key for backwards compatibility
    'status' => [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'archived' => 'مؤرشف',
        'verification_required' => 'بانتظار التحقق في الفرع',
    ],
    
    'actions' => [
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'archive' => 'أرشفة',
        'request_verification' => 'طلب تحقق في الفرع',
    ],
    
    'messages' => [
        'approved_successfully' => 'تمت الموافقة بنجاح',
        'rejected_successfully' => 'تم الرفض بنجاح',
        'archived_successfully' => 'تمت الأرشفة بنجاح',
        'approved' => 'تمت الموافقة بنجاح',
        'verification_required' => 'يرجى إحضار المنتج إلى أحد فروعنا للتحقق والتصوير',
        'verification_requested' => 'تم طلب التحقق بنجاح',
        'verification_user_notice' => 'منتجك بانتظار التحقق - يرجى زيارة أحد فروعنا',
    ],
    
    'placeholders' => [
        'rejection_reason' => 'أدخل سبب الرفض...',
    ],
];
