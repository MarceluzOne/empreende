<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCitizenPhoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Guarda só dígitos: é como CitizenService::phonesFor() compara os números
     * vindos das várias origens, que gravam com e sem máscara.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => preg_replace('/\D/', '', (string) $this->phone)]);
        }
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Informe o telefone.',
            'phone.regex'    => 'Informe um telefone com DDD — 10 ou 11 dígitos.',
        ];
    }
}
