<?php

use Ynotz\EasyAdmin\Services\DashboardService;
use Ynotz\EasyAdmin\Services\SidebarService;

return [
    'dashboard_service' => DashboardService::class,
    'sidebar_service' => SidebarService::class,
    'dashboard_view' => 'easyadmin::admin.dashboard',
    'table_row_counts' => [10, 20, 30]
];
