<?php

namespace App\Services;

use App\Models\ServiceProvider;

class ServiceProviderService
{
    public function store(array $data, $imageFile = null): ServiceProvider
    {
        if ($imageFile) {
            $data['business_image'] = $imageFile->store('service_images', 'public');
        }
        return ServiceProvider::create($data);
    }

    public function update(ServiceProvider $provider, array $data, $imageFile = null): ServiceProvider
    {
        if ($imageFile) {
            if ($provider->business_image) {
                \Storage::disk('public')->delete($provider->business_image);
            }
            $data['business_image'] = $imageFile->store('service_images', 'public');
        }
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
