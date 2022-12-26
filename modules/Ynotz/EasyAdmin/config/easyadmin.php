<?php

use Ynotz\EasyAdmin\Services\DashboardService;
use Ynotz\EasyAdmin\Services\SidebarService;

return [
    'dashboard_sidebar' => [
        [
            'title' => 'Roles',
            'route' => 'roles.index',
            'route_params' => [],
            'icon' => 'icons.users'
        ]
    ],
    'dashboard_service' => DashboardService::class,
    'sidebar_service' => SidebarService::class,
    'dashboard_view' => 'easyadmin::admin.dashboard'
];
