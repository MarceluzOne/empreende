<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Services\ServiceProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'whatsapp'       => 'required|string',
            'instagram'      => 'nullable|string|max:255',
            'optional_info'  => 'nullable|string|max:2000',
            'business_image' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $serviceData = $validator->validated();
        $serviceData['status'] = 'pending';

        try {
            if (!empty($serviceData['business_image'])) {
                $base64 = $serviceData['business_image'];
                if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
                    $ext      = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
                    $data     = base64_decode(substr($base64, strpos($base64, ',') + 1));
                    $filename = 'service_images/' . Str::uuid() . '.' . $ext;
                    Storage::disk('public')->put($filename, $data);
                    $serviceData['business_image'] = $filename;
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
