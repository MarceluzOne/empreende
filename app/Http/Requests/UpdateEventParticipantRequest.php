<?php

namespace App\Http\Requests;

use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventParticipantRequest extends FormRequest
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
        $event       = $this->route('event');
        $participant = $this->route('participant');

        return [
            'name'     => 'required|string|max:255',
            'cpf'      => [
                'required',
                'string',
                new Cpf,
                Rule::unique('event_participants', 'cpf')
                    ->where('event_id', $event->id)
                    ->ignore($participant->id),
            ],
            'whatsapp' => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do participante é obrigatório.',
            'cpf.required'  => 'O CPF é obrigatório — é por ele que o participante busca o certificado.',
            'cpf.unique'    => 'Este CPF já está inscrito neste evento.',
            'email.email'   => 'Informe um e-mail válido.',
        ];
    }
}
