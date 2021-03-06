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
            [
                'title' => 'Log',
                'route' => r('admin.product.provider-logs.index'),
                'icon' => 'fa-folder',
                'permission' => 'product.providerlog.index',
            ],
            [
                'title' => __('product::product.price_report'),
                'route' => r('admin.product.products.price-report'),
                'icon' => 'fa-folder',
                'permission' => 'product.product.index',
            ],
            [
                'title' => __('product::product.refresh_google_table'),
                'route' => r('admin.product.products.refresh-google-table'),
                'icon' => 'fa-folder',
                'permission' => 'product.product.index',
            ],
        ],
    ],
];
