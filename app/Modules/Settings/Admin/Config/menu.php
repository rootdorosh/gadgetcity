<?php

return [
    [
        'module' => 'settings',
        'title' => __('settings::settings.title.index'),
        'route' => r('admin.settings.index'),
        'icon' => 'fa fa-cog fa-fw',
        'permission' => 'settings.settings.index',
        'rank' => 10,
    ],
];
