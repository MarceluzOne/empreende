<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicEventParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normaliza o CPF para apenas dígitos antes de validar, para que a checagem
     * de unicidade compare com o valor efetivamente gravado (sem máscara).
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $digits = preg_replace('/\D/', '', (string) $this->cpf);
            $this->merge(['cpf' => $digits !== '' ? $digits : null]);
        }
    }

    public function rules(): array
    {
        $event = $this->route('event');

        return [
            'name'     => 'required|string|max:255',
            'cpf'      => [
                'required',
                'string',
                new Cpf,
                Rule::unique('event_participants', 'cpf')->where('event_id', $event->id),
            ],
            'whatsapp' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (strlen(preg_replace('/\D/', '', (string) $value)) < 10) {
                        $fail('Informe um WhatsApp válido com DDD.');
                    }
                },
            ],
            'email'    => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Informe seu nome completo.',
            'cpf.required'      => 'Informe o CPF.',
            'cpf.unique'        => 'Este CPF já está inscrito neste evento.',
            'whatsapp.required' => 'Informe um WhatsApp para contato.',
            'email.email'       => 'Informe um e-mail válido.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $event = $this->route('event');

            if ($event->registrationsClosed()) {
                $v->errors()->add('capacity', 'As inscrições deste evento estão encerradas.');
            } elseif ($event->isFull()) {
                $v->errors()->add('capacity', 'As vagas para este evento estão esgotadas.');
            }
        });
    }
}
