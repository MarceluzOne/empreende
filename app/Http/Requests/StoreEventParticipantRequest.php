<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventParticipantRequest extends FormRequest
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
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'cpf'       => [
                'nullable',
                'string',
                'max:14',
                Rule::unique('event_participants', 'cpf')->where('event_id', $event->id),
            ],
            'whatsapp'  => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O nome do participante é obrigatório.',
            'email.email'    => 'Informe um e-mail válido.',
            'cpf.unique'     => 'Este CPF já está inscrito neste evento.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            if ($this->route('event')->isFull()) {
                $v->errors()->add('capacity', 'As vagas para este evento estão esgotadas.');
            }
        });
    }
}
