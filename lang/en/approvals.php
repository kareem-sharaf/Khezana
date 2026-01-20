<?php

return [
    'title' => 'Approvals',
    'singular' => 'Approval',
    'plural' => 'Approvals',
    
    'fields' => [
        'status' => 'Status',
        'approvable_type' => 'Content Type',
        'approvable_title' => 'Content Title',
        'submitted_by' => 'Submitted By',
        'reviewed_by' => 'Reviewed By',
        'reviewed_at' => 'Reviewed At',
        'submitted_at' => 'Submitted At',
        'rejection_reason' => 'Rejection Reason',
    ],
    
    'status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
    ],
    
    'messages' => [
        'approved_successfully' => 'Approved successfully',
        'rejected_successfully' => 'Rejected successfully',
        'archived_successfully' => 'Archived successfully',
    ],
    
    'placeholders' => [
        'rejection_reason' => 'Enter the reason for rejection...',
    ],
];
