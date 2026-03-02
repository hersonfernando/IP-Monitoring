<?php

namespace App\Console\Commands;

use App\Models\IpCheckLog;
use App\Models\MonitoredIp;
use App\Services\IpStatusChecker;
use Illuminate\Console\Command;

class ScanMonitoredIps extends Command
{
    protected $signature = 'ip-monitor:scan';

    protected $description = 'Scan active monitored IP addresses and store latest status';

    public function handle(IpStatusChecker $checker): int
    {
        $ips = MonitoredIp::query()
            ->where('is_active', true)
            ->get();

        foreach ($ips as $ip) {
            $result = $checker->check($ip->ip_address, $ip->port);

            $ip->update([
                'last_status' => $result['status'],
                'last_checked_at' => now(),
                'last_response_ms' => $result['response_ms'],
            ]);

            IpCheckLog::query()->create([
                'monitored_ip_id' => $ip->id,
                'status' => $result['status'],
                'response_ms' => $result['response_ms'],
                'message' => $result['message'],
                'checked_at' => now(),
            ]);
        }

        $this->info("Checked {$ips->count()} IP(s).");

        return self::SUCCESS;
    }
}
