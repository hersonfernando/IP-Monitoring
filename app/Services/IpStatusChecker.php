<?php

namespace App\Services;

class IpStatusChecker
{
    /**
     * @return array{status:string,response_ms:int|null,message:string|null}
     */
    public function check(string $ipAddress, int $port = 80, float $timeoutSeconds = 1.5): array
    {
        $start = microtime(true);
        $errno = 0;
        $errstr = null;

        $connection = @fsockopen($ipAddress, $port, $errno, $errstr, $timeoutSeconds);

        if ($connection === false) {
            return [
                'status' => 'offline',
                'response_ms' => null,
                'message' => $errstr ?: "Connection failed ({$errno})",
            ];
        }

        fclose($connection);

        return [
            'status' => 'online',
            'response_ms' => (int) round((microtime(true) - $start) * 1000),
            'message' => null,
        ];
    }
}
