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
    ],
    
    'status' => [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'archived' => 'مؤرشف',
    ],
    
    'messages' => [
        'approved_successfully' => 'تمت الموافقة بنجاح',
        'rejected_successfully' => 'تم الرفض بنجاح',
        'archived_successfully' => 'تمت الأرشفة بنجاح',
    ],
    
    'placeholders' => [
        'rejection_reason' => 'أدخل سبب الرفض...',
    ],
];
