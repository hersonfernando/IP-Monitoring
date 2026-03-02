<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonitoredIp extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'ip_address',
        'port',
        'is_active',
        'last_status',
        'last_checked_at',
        'last_response_ms',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(IpCheckLog::class);
    }
}
