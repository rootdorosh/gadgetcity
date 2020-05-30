<?php

return [
    [
        'module' => 'user',
        'title' => 'Users',
        'route' => '#',
        'icon' => 'fa-user',
        'permission' => 'user.user.index',
        'rank' => 1,
        'children' => [
            [
                'title' => 'Users',
                'route' => r('admin.user.users.index'),
                'icon' => 'fa-user',
                'permission' => 'user.user.index',
            ],
            [
                'title' => 'Roles',
                'route' => r('admin.user.roles.index'),
                'icon' => 'fa-user-secret',
                'permission' => 'user.role.index',
            ]
        ],
    ],
];
    