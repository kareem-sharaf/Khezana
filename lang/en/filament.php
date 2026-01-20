<?php

return [
    // Navigation Groups
    'navigation_groups' => [
        'content_management' => 'Content Management',
        'user_management' => 'User Management',
        'moderation' => 'Moderation',
        'settings' => 'Settings',
    ],

    // Common Actions
    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'back' => 'Back',
        'search' => 'Search',
        'filter' => 'Filter',
        'export' => 'Export',
        'import' => 'Import',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'archive' => 'Archive',
    ],

    // Common Table Columns
    'table' => [
        'id' => 'ID',
        'name' => 'Name',
        'title' => 'Title',
        'description' => 'Description',
        'status' => 'Status',
        'email' => 'Email',
        'phone' => 'Phone',
        'type' => 'Type',
        'price' => 'Price',
        'owner' => 'Owner',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],

    // Common Status
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'suspended' => 'Suspended',
    ],

    // Common Filters
    'filters' => [
        'active_only' => 'Active Only',
        'inactive_only' => 'Inactive Only',
        'all' => 'All',
    ],

    // Common Sections
    'sections' => [
        'user_information' => 'User Information',
        'roles_permissions' => 'Roles & Permissions',
        'profile_information' => 'Profile Information',
        'approval_information' => 'Approval Information',
        'content_information' => 'Content Information',
        'rejection_reason' => 'Rejection Reason',
    ],

    // Common Messages
    'messages' => [
        'operation_completed' => 'Operation completed successfully',
        'error_occurred' => 'An error occurred',
        'not_found' => 'Not found',
        'unauthorized' => 'Unauthorized',
        'deleted_successfully' => 'Deleted successfully',
        'saved_successfully' => 'Saved successfully',
        'updated_successfully' => 'Updated successfully',
        'created_successfully' => 'Created successfully',
    ],

    // Common Confirmations
    'confirmations' => [
        'delete' => 'Are you sure you want to delete?',
        'deactivate' => 'Are you sure you want to deactivate?',
        'approve' => 'Are you sure you want to approve?',
        'reject' => 'Are you sure you want to reject?',
    ],
];
