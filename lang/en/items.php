<?php

return [
    'title' => 'Listings',
    'singular' => 'Listing',
    'plural' => 'Listings',

    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'operation_type' => 'Operation Type',
        'governorate' => 'Governorate',
        'condition' => 'Condition',
        'price' => 'Price',
        'deposit_amount' => 'Deposit Amount',
        'is_available' => 'Currently Available',
        'category' => 'Category',
        'category_id' => 'Category',
        'user' => 'Owner',
        'owner' => 'Owner',
        'status' => 'Status',
        'images' => 'Images',
        'attributes' => 'Specifications',
    ],

    'conditions' => [
        'new' => 'New',
        'new_hint' => 'Never used before',
        'used' => 'Used',
        'used_hint' => 'Previously used and in good condition',
    ],

    'governorates' => [
        'damascus' => 'Damascus',
        'aleppo' => 'Aleppo',
        'homs' => 'Homs',
        'hama' => 'Hama',
        'latakia' => 'Latakia',
        'tartus' => 'Tartus',
        'daraa' => 'Daraa',
        'sweida' => 'Sweida',
        'hasakah' => 'Hasakah',
        'deir_ezzor' => 'Deir ez-Zor',
        'raqqa' => 'Raqqa',
        'idlib' => 'Idlib',
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
        'created' => 'Listing created successfully',
        'updated' => 'Listing updated successfully',
        'deleted' => 'Listing deleted successfully',
        'submitted_for_approval' => 'Listing submitted for approval successfully',
        'category_required' => 'Category is required',
        'operation_type_required' => 'Operation type is required',
        'operation_type_invalid' => 'Invalid operation type',
        'title_required' => 'Title is required',
        'title_max' => 'Title must not exceed 255 characters',
        'condition_required' => 'Item condition is required',
        'condition_invalid' => 'Invalid item condition',
        'governorate_invalid' => 'Invalid governorate',
        'price_numeric' => 'Price must be a number',
        'price_min' => 'Price must be greater than or equal to zero',
        'deposit_numeric' => 'Deposit amount must be a number',
        'deposit_min' => 'Deposit amount must be greater than or equal to zero',
        'attributes_required' => 'All required attributes must be filled',
        'price_required_for_sell' => 'Price is required for selling',
        'price_required_for_rent' => 'Price is required for renting',
        'deposit_required_for_rent' => 'Deposit amount is required for renting',
        'create_help' => 'Add your listing with all details to make it easier for others to find it.',
        'review_notice' => 'The listing will be reviewed before publishing to ensure quality and safety.',
    ],

    'placeholders' => [
        'title' => 'Example: Evening dress in excellent condition',
        'description' => 'Add a clear description of condition, size, and any important details',
        'price' => 'Leave empty for offers without monetary cost',
        'deposit_amount' => 'Optional for rent',
        'category_id' => 'Select category',
    ],

    'hints' => [
        'category' => 'Choose the appropriate category to make your listing easier to find',
        'operation_type' => 'Choose the method that suits your listing',
        'title' => 'A clear title attracts attention and helps in search',
        'description' => 'The clearer the description, the better the chance of finding what suits you',
        'governorate' => 'Select the governorate where the item is located',
        'condition' => 'Specify item condition: New (never used) or Used (previously used)',
        'price' => 'An appropriate price increases the chance of contact',
        'deposit_amount' => 'Deposit ensures the clothing is returned in good condition',
        'is_available' => 'Uncheck if the listing is temporarily unavailable',
        'images' => 'Clear images increase buyer confidence',
    ],

    'actions' => [
        'create' => 'Create Listing',
        'edit' => 'Edit Listing',
        'delete' => 'Delete Listing',
        'submit_for_approval' => 'Publish Listing',
        'view' => 'View Listing',
    ],

    'messages' => [
        'create_help' => 'Add your listing with all details to make it easier for others to find it.',
    ],
];
