<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficeNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedCidrs = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('OFFICE_ALLOWED_CIDRS', '127.0.0.1/32'))
        )));

        $clientIp = $request->ip();

        foreach ($allowedCidrs as $cidr) {
            if ($this->ipInCidr($clientIp, $cidr)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Access denied. This module is available only inside office network.',
        ], 403);
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            return $ip === $cidr;
        }

        [$subnet, $maskBits] = explode('/', $cidr, 2);
        $maskBits = (int) $maskBits;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - $maskBits);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
