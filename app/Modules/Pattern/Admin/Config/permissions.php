<?php 

return [
    'title' => 'Модуль "Pattern"',
    'items' => [
        'pattern' => [
            'title' => 'Pattern',
            'actions' => [
                'pattern.pattern.index' => 'permission.index',
                'pattern.pattern.store' => 'permission.store',
                'pattern.pattern.update' => 'permission.update',
                'pattern.pattern.show' => 'permission.show',
                'pattern.pattern.destroy' => 'permission.destroy',
            ],
        ],
    ],
];