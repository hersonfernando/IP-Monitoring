<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMonitoredIpRequest;
use App\Http\Requests\UpdateMonitoredIpRequest;
use App\Models\IpCheckLog;
use App\Models\MonitoredIp;
use App\Services\IpStatusChecker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IpMonitorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ips = MonitoredIp::query()
            ->orderBy('label')
            ->paginate((int) $request->integer('per_page', 20));

        return response()->json($ips);
    }

    public function store(StoreMonitoredIpRequest $request): JsonResponse
    {
        $ip = MonitoredIp::query()->create([
            'label' => $request->string('label')->toString(),
            'ip_address' => $request->string('ip_address')->toString(),
            'port' => (int) ($request->input('port') ?? 80),
            'is_active' => $request->boolean('is_active', true),
            'notes' => $request->input('notes'),
        ]);

        return response()->json($ip, 201);
    }

    public function update(UpdateMonitoredIpRequest $request, MonitoredIp $monitoredIp): JsonResponse
    {
        $monitoredIp->update($request->validated());

        return response()->json($monitoredIp->refresh());
    }

    public function destroy(MonitoredIp $monitoredIp): JsonResponse
    {
        $monitoredIp->delete();

        return response()->json(status: 204);
    }

    public function checkNow(MonitoredIp $monitoredIp, IpStatusChecker $checker): JsonResponse
    {
        $result = $checker->check($monitoredIp->ip_address, $monitoredIp->port);

        $monitoredIp->update([
            'last_status' => $result['status'],
            'last_checked_at' => now(),
            'last_response_ms' => $result['response_ms'],
        ]);

        IpCheckLog::query()->create([
            'monitored_ip_id' => $monitoredIp->id,
            'status' => $result['status'],
            'response_ms' => $result['response_ms'],
            'message' => $result['message'],
            'checked_at' => now(),
        ]);

        return response()->json([
            'ip' => $monitoredIp->refresh(),
            'check' => $result,
        ]);
    }

    public function logs(MonitoredIp $monitoredIp): JsonResponse
    {
        return response()->json(
            $monitoredIp->logs()->latest('checked_at')->paginate(50)
        );
    }
}
