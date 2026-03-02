<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpCheckLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitored_ip_id',
        'status',
        'response_ms',
        'message',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function monitoredIp(): BelongsTo
    {
        return $this->belongsTo(MonitoredIp::class);
    }
}
