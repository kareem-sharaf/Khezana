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
        'verification_message' => 'Message to User',
    ],
    
    'statuses' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
        'verification_required' => 'Verification Required',
    ],
    
    // Legacy key for backwards compatibility
    'status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'archived' => 'Archived',
        'verification_required' => 'Verification Required',
    ],
    
    'actions' => [
        'approve' => 'Approve',
        'reject' => 'Reject',
        'archive' => 'Archive',
        'request_verification' => 'Request Branch Verification',
    ],
    
    'messages' => [
        'approved_successfully' => 'Approved successfully',
        'rejected_successfully' => 'Rejected successfully',
        'archived_successfully' => 'Archived successfully',
        'approved' => 'Approved successfully',
        'verification_required' => 'Please bring the item to one of our branches for verification and photography',
        'verification_requested' => 'Verification request sent successfully',
        'verification_user_notice' => 'Your item is awaiting verification - please visit one of our branches',
    ],
    
    'placeholders' => [
        'rejection_reason' => 'Enter the reason for rejection...',
    ],
];
