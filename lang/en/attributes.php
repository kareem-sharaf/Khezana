<?php

return [
    'title' => 'Attributes',
    'singular' => 'Attribute',
    'plural' => 'Attributes',
    
    'fields' => [
        'name' => 'Name',
        'slug' => 'Slug',
        'type' => 'Type',
        'is_required' => 'Required',
        'values' => 'Predefined Values',
        'value' => 'Value',
        'categories' => 'Categories',
    ],
    
    'types' => [
        'text' => 'Text',
        'select' => 'Select',
        'number' => 'Number',
    ],
    
    'messages' => [
        'created' => 'Attribute created successfully',
        'updated' => 'Attribute updated successfully',
        'deleted' => 'Attribute deleted successfully',
        'assigned_to_category' => 'Attribute assigned to category successfully',
        'removed_from_category' => 'Attribute removed from category successfully',
        'value_required' => 'This attribute is required',
        'invalid_value' => 'Invalid value',
    ],
    
    'placeholders' => [
        'name' => 'Enter attribute name',
        'slug' => 'Will be auto-generated from name',
        'type' => 'Select attribute type',
        'value' => 'Enter value',
    ],
    
    'helpers' => [
        'type_select' => 'Select: predefined values',
        'type_text' => 'Text: free text input',
        'type_number' => 'Number: numeric value only',
        'is_required' => 'This attribute must be filled when creating items',
        'values' => 'Add predefined values for select type attributes',
    ],
];
