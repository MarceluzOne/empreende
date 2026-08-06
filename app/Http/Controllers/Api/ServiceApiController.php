<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use App\Services\ServiceProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceApiController extends Controller
{
    protected $serviceProviderService;

    // Injeção de dependência no construtor
    public function __construct(ServiceProviderService $serviceProviderService)
    {
        $this->serviceProviderService = $serviceProviderService;
    }

    public function store(Request $request)
    {
        // 1. Validação dos dados vindos da página externa
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'provider_type'  => 'required|in:individual,company',
            'service_title'  => 'required|string|max:255',
            'email'          => 'required|email',
            // Documento por tipo: prestador individual manda CPF, empresa
            // manda CNPJ. É a chave que liga o cadastro à conta no portal.
            'cpf'            => ['required_if:provider_type,individual', 'nullable', new Cpf],
            'cnpj'           => ['required_if:provider_type,company', 'nullable', new Cnpj],
            'whatsapp'       => 'required|string',
            'instagram'      => 'nullable|string|max:255',
            'optional_info'  => 'nullable|string|max:2000',
            'business_image' => 'nullable|string',
        ], [
            // O projeto não tem lang/pt_BR/validation.php: sem estas mensagens
            // o formulário mostraria "validation.required" no toast.
            'name.required'          => 'Informe seu nome completo.',
            'service_title.required' => 'Informe o que você oferece.',
            'email.required'         => 'Informe seu e-mail.',
            'email.email'            => 'Informe um e-mail válido.',
            'cpf.required_if'        => 'Informe seu CPF.',
            'cnpj.required_if'       => 'Informe o CNPJ da empresa.',
            'whatsapp.required'      => 'Informe seu WhatsApp.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $serviceData = $validator->validated();
        $serviceData['status'] = 'pending';
        // Documentos chegam com máscara do formulário e são gravados só em
        // dígitos: é assim que o portal casa o cadastro com a conta.
        foreach (['cpf', 'cnpj'] as $documento) {
            if (!empty($serviceData[$documento])) {
                $serviceData[$documento] = preg_replace('/\D/', '', $serviceData[$documento]);
            }
        }

        try {
            if (!empty($serviceData['business_image'])) {
                $path = $this->serviceProviderService->storeBase64Image($serviceData['business_image']);

                if ($path) {
                    $serviceData['business_image'] = $path;
                } else {
                    unset($serviceData['business_image']);
                }
            }

            $service = ServiceProvider::create($serviceData);

            return response()->json([
                'success' => true,
                'message' => 'Cadastro recebido com sucesso e aguardando aprovação.',
                'data' => $service,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar cadastro externo.',
            ], 500);
        }
    }

    public function getGroupedProviders()
    {
        $data = $this->serviceProviderService->getActiveGroupedProviders();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
