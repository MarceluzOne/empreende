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
