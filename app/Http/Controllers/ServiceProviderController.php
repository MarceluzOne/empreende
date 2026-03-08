<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index()
    {
        $providers = ServiceProvider::latest()->paginate(10);
        return view('services.index', compact('providers'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'provider_type'  => 'required|in:individual,company',
            'service_title'  => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'whatsapp'       => 'required|string|max:20',
            'instagram'      => 'nullable|string|max:100',
            'optional_info'  => 'nullable|string',
        ], [
            'provider_type.in' => 'Selecione se você é Pessoa Física ou Empresa.',
            'name.required'    => 'O nome é obrigatório.',
            'whatsapp.required' => 'O WhatsApp é essencial para o contato.'
        ]);

        ServiceProvider::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Prestador de serviço cadastrado com sucesso!');
    }

    public function edit(ServiceProvider $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, ServiceProvider $service)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'provider_type'  => 'required|in:individual,company',
            'service_title'  => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'whatsapp'       => 'required|string|max:20',
            'instagram'      => 'nullable|string|max:100',
            'optional_info'  => 'nullable|string',
            'status' => 'required|in:active,inactive,pending',
        ], [
            'provider_type.in' => 'Selecione se você é Pessoa Física ou Empresa.',
            'name.required'    => 'O nome é obrigatório.'
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', "Os dados de {$service->name} foram atualizados!");
    }

    public function show(ServiceProvider $service)
    {
        return response()->json($service);
    }

    public function destroy(ServiceProvider $service)
    {
        $service->delete();
        return redirect()->route('services.index')
            ->with('success', 'Registro removido conforme solicitado.');
    }
}