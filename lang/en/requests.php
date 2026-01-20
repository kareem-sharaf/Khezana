<?php

return [
    'title' => 'Requests',
    'singular' => 'Request',
    'plural' => 'Requests',
    
    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'status' => 'Status',
        'category' => 'Category',
        'category_id' => 'Category',
        'user' => 'Owner',
        'owner' => 'Owner',
        'attributes' => 'Attributes',
    ],
    
    'status' => [
        'open' => 'Open',
        'fulfilled' => 'Fulfilled',
        'closed' => 'Closed',
    ],
    
    'messages' => [
        'created_successfully' => 'Request created successfully',
        'updated_successfully' => 'Request updated successfully',
        'deleted_successfully' => 'Request deleted successfully',
        'submitted_for_approval' => 'Request submitted for approval',
        'closed_successfully' => 'Request closed successfully',
        'pending_approval' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'no_requests' => 'No requests found',
        'no_description' => 'No description',
        'cannot_edit_closed' => 'Cannot edit a closed or fulfilled request',
    ],
    
    'placeholders' => [
        'title' => 'Enter request title',
        'description' => 'Enter request description',
        'category_id' => 'Select category',
    ],
    
    'actions' => [
        'create' => 'Create Request',
        'edit' => 'Edit Request',
        'delete' => 'Delete Request',
        'submit_for_approval' => 'Submit for Approval',
        'view' => 'View Request',
        'update' => 'Update',
        'close' => 'Close Request',
    ],
];
