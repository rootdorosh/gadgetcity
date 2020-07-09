<?php 

return [
    'title' => 'Модуль "Color"',
    'items' => [
        'color' => [
            'title' => 'Color',
            'actions' => [
                'color.color.index' => 'permission.index',
                'color.color.store' => 'permission.store',
                'color.color.update' => 'permission.update',
                'color.color.show' => 'permission.show',
                'color.color.destroy' => 'permission.destroy',
            ],
        ],
    ],
];