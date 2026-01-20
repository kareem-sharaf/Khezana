<?php

return [
    'title' => 'Items',
    'singular' => 'Item',
    'plural' => 'Items',
    
    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'operation_type' => 'Operation Type',
        'price' => 'Price',
        'deposit_amount' => 'Deposit Amount',
        'is_available' => 'Is Available',
        'category' => 'Category',
        'category_id' => 'Category',
        'user' => 'Owner',
        'owner' => 'Owner',
        'status' => 'Status',
        'images' => 'Images',
    ],
    
    'operation_types' => [
        'sell' => 'Sell',
        'rent' => 'Rent',
        'donate' => 'Donate',
    ],
    
    'availability' => [
        'available' => 'Available',
        'unavailable' => 'Unavailable',
    ],
    
    'messages' => [
        'created' => 'Item created successfully',
        'updated' => 'Item updated successfully',
        'deleted' => 'Item deleted successfully',
        'submitted_for_approval' => 'Item submitted for approval successfully',
        'category_required' => 'Category is required',
        'attributes_required' => 'All required attributes must be filled',
        'price_required_for_sell' => 'Price is required for selling',
        'price_required_for_rent' => 'Price is required for renting',
        'deposit_required_for_rent' => 'Deposit amount is required for renting',
    ],
    
    'placeholders' => [
        'title' => 'Enter item title',
        'description' => 'Enter item description',
        'price' => 'Enter price',
        'deposit_amount' => 'Enter deposit amount',
        'category_id' => 'Select category',
    ],
    
    'actions' => [
        'create' => 'Create Item',
        'edit' => 'Edit Item',
        'delete' => 'Delete Item',
        'submit_for_approval' => 'Submit for Approval',
        'view' => 'View Item',
    ],
];
