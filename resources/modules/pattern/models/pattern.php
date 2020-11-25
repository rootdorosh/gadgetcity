<?php

return [
    'id' => '01',
    'name' => 'Pattern',
    'name_plural' => 'Patterns',
    'table' => 'pattern',
    'menu' => [
        'icon' => 'fa-folder',
    ],
    'fields' => [
        'example' => [
            'label' => 'Пример',
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
        'value' => [
            'label' => 'Шаблон',
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
        'rank' => [
            'label' => 'Порядок применения',
            'required' => true,
            'type' => 'integer',
            'field' => [
                'type' => 'text',
            ],
            'rules' => [
            ],
            'faker' => null,
            'filter' => true,
            'migration' => [
                'type' => 'integer',
            ],
        ],
    ],
    'classMap' => [
        'skipRequest' => ['BulkToggleRequest'],
    ],
    'routes' => [
        'path' => 'patterns',
    ],
];
