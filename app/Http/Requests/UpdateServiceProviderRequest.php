<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Edição do cadastro de prestador feita pelo próprio dono, dentro do portal.
 * E-mail e CPF ficam de fora: são as chaves que ligam o cadastro à conta.
 */
class UpdateServiceProviderRequest extends FormRequest
{
    /**
     * Bag próprio: a tela do portal tem outros formulários e só o modal de
     * serviço deve reagir a estes erros.
     */
    protected $errorBag = 'servico';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'service_title'  => 'required|string|max:255',
            'whatsapp'       => 'required|string|max:20',
            'instagram'      => 'nullable|string|max:255',
            'optional_info'  => 'nullable|string|max:2000',
            // Chega como data URI: o recorte 16:9 é feito no navegador.
            'business_image' => ['nullable', 'string', 'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/', 'max:7000000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Informe o nome.',
            'service_title.required' => 'Informe o que você oferece.',
            'whatsapp.required'      => 'Informe o WhatsApp de contato.',
            'business_image.regex'   => 'Envie a foto em JPG, PNG ou WEBP.',
            'business_image.max'     => 'A imagem deve ter no máximo 5MB.',
        ];
    }
}
