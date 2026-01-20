<?php

return [
    'title' => 'الطلبات',
    'singular' => 'طلب',
    'plural' => 'طلبات',
    
    'fields' => [
        'title' => 'العنوان',
        'description' => 'الوصف',
        'status' => 'الحالة',
        'category' => 'الفئة',
        'category_id' => 'الفئة',
        'user' => 'المالك',
        'owner' => 'المالك',
        'attributes' => 'الخصائص',
    ],
    
    'status' => [
        'open' => 'مفتوح',
        'fulfilled' => 'مكتمل',
        'closed' => 'مغلق',
    ],
    
    'messages' => [
        'created_successfully' => 'تم إنشاء الطلب بنجاح',
        'updated_successfully' => 'تم تحديث الطلب بنجاح',
        'deleted_successfully' => 'تم حذف الطلب بنجاح',
        'submitted_for_approval' => 'تم إرسال الطلب للموافقة',
        'closed_successfully' => 'تم إغلاق الطلب بنجاح',
        'pending_approval' => 'قيد الانتظار للموافقة',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
        'no_requests' => 'لا توجد طلبات',
        'no_description' => 'لا يوجد وصف',
        'cannot_edit_closed' => 'لا يمكن تعديل طلب مغلق أو مكتمل',
    ],
    
    'placeholders' => [
        'title' => 'أدخل عنوان الطلب',
        'description' => 'أدخل وصف الطلب',
        'category_id' => 'اختر الفئة',
    ],
    
    'actions' => [
        'create' => 'إنشاء طلب',
        'edit' => 'تعديل الطلب',
        'delete' => 'حذف الطلب',
        'submit_for_approval' => 'إرسال للموافقة',
        'view' => 'عرض الطلب',
        'update' => 'تحديث',
        'close' => 'إغلاق الطلب',
    ],
];
