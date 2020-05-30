<?php 

return [
    'title' => 'Модуль "Product"',
    'items' => [
        'price' => [
            'title' => 'Price',
            'actions' => [
                'product.price.index' => 'permission.index',
                'product.price.store' => 'permission.store',
                'product.price.update' => 'permission.update',
                'product.price.show' => 'permission.show',
                'product.price.destroy' => 'permission.destroy',
            ],
        ],
        'product' => [
            'title' => 'Product',
            'actions' => [
                'product.product.index' => 'permission.index',
                'product.product.store' => 'permission.store',
                'product.product.update' => 'permission.update',
                'product.product.show' => 'permission.show',
                'product.product.destroy' => 'permission.destroy',
            ],
        ],
        'provider' => [
            'title' => 'Provider',
            'actions' => [
                'product.provider.index' => 'permission.index',
                'product.provider.store' => 'permission.store',
                'product.provider.update' => 'permission.update',
                'product.provider.show' => 'permission.show',
                'product.provider.destroy' => 'permission.destroy',
            ],
        ],
        'providerItem' => [
            'title' => 'ProviderItem',
            'actions' => [
                'product.providerItem.index' => 'permission.index',
                'product.providerItem.store' => 'permission.store',
                'product.providerItem.update' => 'permission.update',
                'product.providerItem.show' => 'permission.show',
                'product.providerItem.destroy' => 'permission.destroy',
            ],
        ],
    ],
];