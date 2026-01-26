<?php

return [
    'title' => 'Branches',
    'singular' => 'Branch',
    'plural' => 'Branches',

    'sections' => [
        'basic_info' => 'Basic Information',
        'contact_info' => 'Contact Information',
        'location' => 'Location',
        'working_hours' => 'Working Hours',
    ],

    'fields' => [
        'name' => 'Branch Name',
        'code' => 'Branch Code',
        'address' => 'Address',
        'city' => 'City',
        'latitude' => 'Latitude',
        'longitude' => 'Longitude',
        'phone' => 'Phone',
        'email' => 'Email',
        'working_hours' => 'Working Hours',
        'is_active' => 'Active',
        'admins_count' => 'Staff Count',
        'day' => 'Day',
        'hours' => 'Hours',
    ],

    'placeholders' => [
        'name' => 'Enter branch name',
        'code' => 'e.g., DMQ',
        'address' => 'Enter branch address',
        'city' => 'Select city',
        'latitude' => 'e.g., 24.7136',
        'longitude' => 'e.g., 46.6753',
        'phone' => 'Enter phone number',
        'email' => 'Enter email address',
        'select_branch' => 'Select branch',
    ],

    'hints' => [
        'code' => 'A unique short code for the branch (2-10 characters)',
        'user_branch' => 'The branch this user belongs to',
    ],

    'filters' => [
        'active_only' => 'Active Only',
        'inactive_only' => 'Inactive Only',
    ],

    'actions' => [
        'create' => 'Create Branch',
        'edit' => 'Edit Branch',
        'delete' => 'Delete Branch',
        'view' => 'View Branch',
        'add_day' => 'Add Day',
    ],

    'messages' => [
        'created_successfully' => 'Branch created successfully',
        'updated_successfully' => 'Branch updated successfully',
        'deleted_successfully' => 'Branch deleted successfully',
        'cannot_delete_with_users' => 'Cannot delete branch with assigned users',
    ],
];
