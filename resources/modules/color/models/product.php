<?php

return [
    'id' => '01',
    'name' => 'Color',
    'name_plural' => 'Colors',
    'table' => 'color',
    'menu' => [
        'icon' => 'fa-folder',
    ],
    'fields' => [
        'title' => [
            'label' => 'Название',
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
        'code' => [
            'label' => 'Код',
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
    ],
    'classMap' => [
        'skipRequest' => ['BulkToggleRequest'],
    ],
    'routes' => [
        'path' => 'colors',
    ],
];
