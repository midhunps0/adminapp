<?php

namespace Ynotz\EasyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;
use Ynotz\EasyAdmin\Services\DashboardServiceInterface;

class MasterController extends SmartController
{
    public function fetch($service, $method)
    {
        try {
            return response()->json([
                'success' => true,
                'results' => (app()->make($service))->$method($this->request)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }
}
