<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceProviderService
{
    /**
     * Salva uma imagem recebida como data URI — é assim que o recorte 16:9
     * feito no navegador (cropper.js) chega, tanto no cadastro público quanto
     * na edição pelo portal. Devolve o caminho no disco ou null se o conteúdo
     * não for um data URI de imagem.
     */
    public function storeBase64Image(string $base64): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            return null;
        }

        $ext  = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
        $path = 'service_images/'.Str::uuid().'.'.$ext;

        Storage::disk('public')->put($path, base64_decode(substr($base64, strpos($base64, ',') + 1)));

        return $path;
    }

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

    /**
     * Edição feita pelo próprio prestador no portal: volta para a fila de
     * aprovação, para que nenhum texto ou foto entre no site sem passar pela
     * equipe.
     */
    public function updateFromPortal(ServiceProvider $provider, array $data, ?string $base64Image = null): ServiceProvider
    {
        $data['status'] = 'pending';

        if ($base64Image && $path = $this->storeBase64Image($base64Image)) {
            if ($provider->business_image) {
                Storage::disk('public')->delete($provider->business_image);
            }
            $data['business_image'] = $path;
        }

        $provider->update($data);

        return $provider;
    }

    /**
     * Cadastros de /servicos do candidato: o vínculo é o CPF informado no
     * cadastro ou o e-mail da conta.
     */
    public function forCandidate(string $email, ?string $cpf)
    {
        return $this->ownedBy($email, $cpf, 'cpf');
    }

    /**
     * Cadastros de /empresas-locais da empresa: mesma ideia, pelo CNPJ.
     */
    public function forCompany(string $email, ?string $cnpj)
    {
        return $this->ownedBy($email, $cnpj, 'cnpj');
    }

    public function belongsToCandidate(ServiceProvider $provider, string $email, ?string $cpf): bool
    {
        return $this->isOwner($provider, $email, $cpf, 'cpf');
    }

    public function belongsToCompany(ServiceProvider $provider, string $email, ?string $cnpj): bool
    {
        return $this->isOwner($provider, $email, $cnpj, 'cnpj');
    }

    public function destroy(ServiceProvider $provider): void
    {
        $provider->delete();
    }

    private function ownedBy(string $email, ?string $document, string $column)
    {
        return ServiceProvider::where(function ($q) use ($email, $document, $column) {
                $q->where('email', $email);

                if ($document !== null) {
                    $q->orWhere($column, $document);
                }
            })
            ->orderBy('name')
            ->get();
    }

    private function isOwner(ServiceProvider $provider, string $email, ?string $document, string $column): bool
    {
        return $provider->email === $email
            || ($document !== null && $provider->{$column} === $document);
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
