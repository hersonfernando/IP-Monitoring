<?php

use App\Http\Controllers\Api\IpMonitorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['office.network'])->prefix('ip-monitoring')->group(function (): void {
    Route::get('/ips', [IpMonitorController::class, 'index']);
    Route::post('/ips', [IpMonitorController::class, 'store']);
    Route::put('/ips/{monitoredIp}', [IpMonitorController::class, 'update']);
    Route::delete('/ips/{monitoredIp}', [IpMonitorController::class, 'destroy']);
    Route::post('/ips/{monitoredIp}/check', [IpMonitorController::class, 'checkNow']);
    Route::get('/ips/{monitoredIp}/logs', [IpMonitorController::class, 'logs']);
});
