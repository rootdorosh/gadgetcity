<?php

return [
    'id' => '04',
    'name' => 'ProviderItem',
    'name_plural' => 'ProviderItems',
    'table' => 'product_providers_items',
    'menu' => [
        'icon' => 'fa-folder',
    ],
    'fields' => [
        'provider_id' => [
            'label' => 'Provider',
            'required' => true,
            'type' => 'integer',
            'rules' => [
                'integer',
                'exists:product_providers,id',
            ],
            'relation' => [
                'name' => 'provider',
                'type' => 'BelongsTo',
                'model' => 'App\Modules\Product\Models\Provider',
            ],
            'migration' => [
                'foreign' => ['product_providers', 'CASCADE'],
                'type' => 'unsignedInteger',
            ],
            'filter' => true,
        ],
        'title' => [
            'label' => 'Product title',
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
        'product_id' => [
            'label' => 'Product',
            'required' => true,
            'type' => 'integer',
            'rules' => [
                'integer',
                'exists:product,id',
            ],
            'relation' => [
                'name' => 'product',
                'type' => 'BelongsTo',
                'model' => 'App\Modules\Product\Models\Product',
            ],
            'migration' => [
                'foreign' => ['product', 'SET NULL'],
                'type' => 'unsignedInteger',
                'nullable' => true,
            ],
            'filter' => true,
        ],
        'status' => [
            'label' => 'Status',
            'required' => true,
            'type' => 'integer',
            'rules' => [
                'integer',
            ],
            'migration' => [
                'type' => 'boolean',
                'default' => '1',
            ],
            'filter' => true,
        ],
        'price' => [
            'label' => 'Price',
            'type' => 'float',
            'field' => [
                'type' => 'text',
            ],
            'required' => true,
            'rules' => [
            ],
            'migration' => [
                'type' => 'double',
                'length' => [10, 2],
                'nullable' => true,
            ],
        ],

    ],
    'classMap' => [
        'skipRequest' => ['BulkToggleRequest'],
    ],
    'routes' => [
        'path' => 'provider-items',
    ],
];
