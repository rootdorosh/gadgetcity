<?php

return [
    'title' => 'Модуль "Лог"',
    'items' => [
        'log' => [
            'title' => 'Лог',
            'actions' => [
                'log.log.index' => 'permission.index',
                'log.log.destroy' => 'permission.destroy',
            ],
        ],
    ]
];
