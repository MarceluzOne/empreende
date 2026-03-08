<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
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
            'name' => 'required|string|max:255',
            'provider_type' => 'required|in:individual,company',
            'service_title' => 'required|string|max:255',
            'email' => 'required|email',
            'whatsapp' => 'required|string',
            // Não validamos o status aqui pois ele será forçado via código
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // 2. Criação do registro forçando o status 'pending'
        $serviceData = $validator->validated();
        $serviceData['status'] = 'pending'; // Regra de negócio: externo sempre pendente

        try {
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
