<?php

return [
    'resources' => [
        'categories' => [
            'label' => 'Category',
            'plural_label' => 'Categories',
            'navigation_label' => 'Categories',
            'navigation_group' => 'Content Management',
        ],
        'attributes' => [
            'label' => 'Attribute',
            'plural_label' => 'Attributes',
            'navigation_label' => 'Attributes',
            'navigation_group' => 'Content Management',
        ],
        'products' => [
            'label' => 'Product',
            'plural_label' => 'Products',
            'navigation_label' => 'Products',
            'navigation_group' => 'Content Management',
        ],
        'users' => [
            'label' => 'User',
            'plural_label' => 'Users',
            'navigation_label' => 'Users',
            'navigation_group' => 'User Management',
        ],
        'approvals' => [
            'label' => 'Approval',
            'plural_label' => 'Approvals',
            'navigation_label' => 'Approvals',
            'navigation_group' => 'Content Management',
        ],
        'roles' => [
            'label' => 'Role',
            'plural_label' => 'Roles',
            'navigation_label' => 'Roles',
            'navigation_group' => 'User Management',
        ],
        'permissions' => [
            'label' => 'Permission',
            'plural_label' => 'Permissions',
            'navigation_label' => 'Permissions',
            'navigation_group' => 'User Management',
        ],
    ],
    
    'actions' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'archive' => 'Archive',
    ],
    
    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Name',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'active' => 'Active Only',
            'inactive' => 'Inactive Only',
            'all' => 'All',
        ],
    ],
];
