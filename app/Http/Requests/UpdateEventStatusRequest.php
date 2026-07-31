<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->roles->contains('name', 'admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:active,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Informe o status do evento.',
            'status.in'       => 'Status inválido para o evento.',
        ];
    }
}
