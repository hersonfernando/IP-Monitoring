<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpCheckLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:online,offline'],
            'response_ms' => ['nullable', 'integer', 'min:0'],
            'message' => ['nullable', 'string', 'max:500'],
        ];
    }
}
