<?php

namespace App\Http\Requests;

use App\Models\MonitoredIp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMonitoredIpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var MonitoredIp|null $ip */
        $ip = $this->route('monitoredIp');

        return [
            'label' => ['sometimes', 'string', 'max:120'],
            'ip_address' => [
                'sometimes',
                'ip',
                'max:45',
                Rule::unique('monitored_ips', 'ip_address')->ignore($ip?->id),
            ],
            'port' => ['sometimes', 'integer', 'between:1,65535'],
            'is_active' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
