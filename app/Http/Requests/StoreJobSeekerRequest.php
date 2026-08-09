<?php

namespace App\Http\Requests;

use App\Models\JobSeeker;
use App\Rules\Cpf;
use App\Support\Document;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobSeekerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                     => 'required|string|max:255',
            'cpf'                      => ['required', 'string', 'max:14', new Cpf, $this->uniqueCpf()],
            'job_function'             => 'required|string|max:255',
            'city'                     => 'nullable|string|max:100',
            'state'                    => 'nullable|string|max:2',
            'phone'                    => 'nullable|string|max:20',
            'email'                    => 'nullable|email|max:255',
            'linkedin_url'             => 'nullable|url|max:255',
            'github_url'               => 'nullable|url|max:255',
            'summary'                  => 'nullable|string|max:2000',
            'skills'                   => 'nullable|string|max:1000',
            'interest_area'            => 'required|string|max:100',
            'experience'               => 'nullable|string|max:50',
            'experiences'              => 'nullable|array',
            'experiences.*.company'    => 'nullable|string|max:255',
            'experiences.*.role'       => 'nullable|string|max:255',
            'experiences.*.start'      => 'nullable|string|max:20',
            'experiences.*.end'        => 'nullable|string|max:20',
            'experiences.*.activities' => 'nullable|string|max:2000',
            'education'                => 'nullable|array',
            'education.*.course'       => 'nullable|string|max:255',
            'education.*.institution'  => 'nullable|string|max:255',
            'education.*.year'         => 'nullable|string|max:50',
            'languages'                => 'nullable|array',
            'languages.*.language'     => 'nullable|string|max:100',
            'languages.*.level'        => 'nullable|string|max:50',
            'certifications'           => 'nullable|array',
            'certifications.*'         => 'nullable|string|max:255',
        ];
    }

    /**
     * Um CPF por candidato: cadastro duplicado quebraria o reconhecimento do
     * currículo no portal, que localiza a pessoa pelo CPF e pega o primeiro
     * resultado. Compara pelas duas grafias porque o CPF é gravado com máscara
     * aqui e só com dígitos nas outras origens.
     *
     * A mensagem cita o nome porque só funcionário autenticado vê esta tela, e
     * saber de quem é o cadastro evita procurar na listagem.
     */
    private function uniqueCpf(): Closure
    {
        return function ($attribute, $value, $fail) {
            $variants = Document::cpfVariants($value);

            if ($variants === []) {
                return;
            }

            $existente = JobSeeker::whereIn('cpf', $variants)->first();

            if ($existente !== null) {
                $fail('Já existe um candidato cadastrado com este CPF: '.$existente->name.'.');
            }
        };
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'O nome é obrigatório.',
            'cpf.required'           => 'O CPF é obrigatório.',
            'job_function.required'  => 'Informe a função desejada.',
            'interest_area.required' => 'Selecione uma área de interesse.',
        ];
    }
}
