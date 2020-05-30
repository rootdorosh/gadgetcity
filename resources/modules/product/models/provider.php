<?php

return [
    'id' => '02',
    'name' => 'Provider',
    'name_plural' => 'Providers',
    'table' => 'product_providers',
    'menu' => [
        'icon' => 'fa-folder',
    ],
    'fields' => [
        'pid' => [
            'label' => 'Pid',
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
        'last_guid' => [
            'label' => 'Last guid',
            'required' => false,
            'type' => 'integer',
            'rules' => [
            ],
            'filter' => false,
            'faker' => 'null',
            'migration' => [
                'type' => 'integer',
                'nullable' => true,
            ],
        ],

    ],
    'classMap' => [
        'skipRequest' => ['BulkToggleRequest'],
    ],
    'routes' => [
        'path' => 'providers',
    ],
];
