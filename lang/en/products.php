<?php

return [
    'title' => 'Products',
    'singular' => 'Product',
    'plural' => 'Products',
    
    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'type' => 'Operation Type',
        'price' => 'Price',
        'category' => 'Category',
        'category_id' => 'Category',
        'user' => 'Owner',
        'status' => 'Status',
    ],
    
    'types' => [
        'sell' => 'Sell',
        'rent' => 'Rent',
        'donate' => 'Donate',
    ],
    
    'messages' => [
        'created' => 'Product created successfully',
        'updated' => 'Product updated successfully',
        'deleted' => 'Product deleted successfully',
        'category_required' => 'Category is required',
        'attributes_required' => 'All required attributes must be filled',
    ],
    
    'placeholders' => [
        'title' => 'Enter product title',
        'description' => 'Enter product description',
        'price' => 'Enter price',
        'category_id' => 'Select category',
    ],
];
