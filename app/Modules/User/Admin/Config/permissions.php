<?php

return [
    'title' => 'Модуль "Пользователи"',
    'items' => [
        'user' => [
            'title' => 'Пользователи',
            'actions' => [
                'user.user.index' => 'permission.index',
                'user.user.store' => 'permission.store',
                'user.user.update' => 'permission.update',
                'user.user.destroy' => 'permission.destroy',
                'user.user.show' => 'permission.show',
            ],
        ],
        'role' => [
            'title' => 'Роли',
            'actions' => [
                'user.role.index' => 'permission.index',
                'user.role.store' => 'permission.store',
                'user.role.update' => 'permission.update',
                'user.role.destroy' => 'permission.destroy',
                'user.role.show' => 'permission.show',
            ],
        ],
    ]
];
