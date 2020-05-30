<?php

return [
    'id' => '03',
    'name' => 'Price',
    'name_plural' => 'Prices',
    'table' => 'product_prices',
	'migration' => [
		'skipId' => true,
		'unique' => "['product_id', 'provider_id']",
	],
	'fields' => [
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
                'foreign' => ['product', 'CASCADE'],
                'type' => 'unsignedInteger',
            ],
        ],
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
            ],
        ],
    ],
    'skipMap' => [
		'menu',
		'permission',
		'request',
		'controller',
		'view',
		'routes',
		'crudService',
		'factory',
	],
];
