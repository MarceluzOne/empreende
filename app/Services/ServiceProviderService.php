<?php

namespace App\Services;

use App\Models\ServiceProvider;

class ServiceProviderService
{
    public function store(array $data): ServiceProvider
    {
        return ServiceProvider::create($data);
    }

    public function update(ServiceProvider $provider, array $data): ServiceProvider
    {
        $provider->update($data);
        return $provider;
    }

    public function destroy(ServiceProvider $provider): void
    {
        $provider->delete();
    }

    public function getActiveGroupedProviders(): array
    {
        $providers = ServiceProvider::where('status', 'active')
            ->get()
            ->groupBy('provider_type');

        return [
            'individuals' => $providers->get('individual', collect([])),
            'companies'   => $providers->get('company', collect([])),
        ];
    }
}
