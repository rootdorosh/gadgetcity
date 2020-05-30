<?php

return [
    'id' => '01',
    'name' => 'Product',
    'name_plural' => 'Products',
    'table' => 'product',
    'menu' => [
        'icon' => 'fa-folder',
    ],
    'fields' => [
        'title' => [
            'label' => 'Title',
            'required' => true,
            'type' => 'string',
            'field' => [
                'type' => 'text',
            ],
            'rules' => [
            ],
            'faker' => null,
            'filter' => true,
            'migration' => [
                'type' => 'string',
            ],
        ],
        'is_active' => [
            'label' => 'Active',
            'required' => true,
            'type' => 'integer',
            'field' => [
                'type' => 'toggle',
            ],
            'rules' => [
                'in:0,1',
            ],
            'filter' => true,
            'faker' => 'rand(0,1)',
            'migration' => [
                'type' => 'boolean',
                'default' => 0,
            ],
        ],
    ],
    'classMap' => [
        'skipRequest' => ['BulkToggleRequest'],
    ],
    'routes' => [
        'path' => 'products',
    ],
];
