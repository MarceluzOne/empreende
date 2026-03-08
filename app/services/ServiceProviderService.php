<?php

namespace App\Services;

use App\Models\ServiceProvider;

class ServiceProviderService
{
    public function getActiveGroupedProviders()
    {
        // Regra de negócio: Apenas status 'active'
        $providers = ServiceProvider::where('status', 'active')
            ->get()
            ->groupBy('provider_type');

        return [
            'individuals' => $providers->get('individual', collect([])),
            'companies'   => $providers->get('company', collect([]))
        ];
    }
} 