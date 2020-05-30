<?php 

return [
    [
        'module' => 'product',
        'title' => 'Products',
        'route' => '#',
        'icon' => ' fa-sitemap',
        'permission' => 'product.product.index',
        'rank' => 1,
        'children' => [
            [
                'title' => 'Products',
                'route' => r('admin.product.products.index'),
                'icon' => 'fa-folder',
                'permission' => 'product.product.index',
            ],
            [
                'title' => 'Providers',
                'route' => r('admin.product.providers.index'),
                'icon' => 'fa-folder',
                'permission' => 'product.provider.index',
            ],
            [
                'title' => 'ProviderItems',
                'route' => r('admin.product.provider-items.index'),
                'icon' => 'fa-folder',
                'permission' => 'product.provideritem.index',
            ],
        ],
    ],
];
