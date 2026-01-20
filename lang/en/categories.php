<?php

return [
    'title' => 'Categories',
    'singular' => 'Category',
    'plural' => 'Categories',
    
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'description' => 'Description',
        'parent_id' => 'Parent Category',
        'is_active' => 'Active',
        'parent' => 'Parent',
        'children' => 'Children',
    ],
    
    'messages' => [
        'created' => 'Category created successfully',
        'updated' => 'Category updated successfully',
        'deleted' => 'Category deleted successfully',
        'cannot_delete_has_children' => 'Cannot delete category because it has children',
        'cannot_be_own_parent' => 'Category cannot be its own parent',
        'circular_reference' => 'Cannot set this parent because it would create a circular reference',
    ],
    
    'placeholders' => [
        'name' => 'Enter category name',
        'slug' => 'Will be auto-generated from name',
        'description' => 'Enter category description',
        'parent_id' => 'Select parent category (optional)',
    ],
];
