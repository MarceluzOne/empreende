<?php

namespace App\Services;

use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceProviderService
{
    /**
     * Cabeçalho aceito para o data URI da foto. Usado na validação das duas
     * entradas (API pública e portal) para que nenhuma fique mais frouxa.
     */
    public const IMAGE_DATA_URI_RULE = 'regex:/^data:image\/(jpeg|jpg|png|webp);base64,/';

    /** Tamanho máximo da imagem já decodificada. */
    private const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    /**
     * Extensão gravada, pelo tipo REAL detectado no conteúdo — nunca pelo
     * cabeçalho do data URI, que vem do cliente.
     */
    private const IMAGE_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Salva uma imagem recebida como data URI — é assim que o recorte 16:9
     * feito no navegador (cropper.js) chega, tanto no cadastro público quanto
     * na edição pelo portal. Devolve o caminho no disco, ou null se o conteúdo
     * não for de fato um JPG, PNG ou WEBP.
     *
     * A rota pública é anônima e grava em disco servido pela web: a extensão
     * já veio do cabeçalho, e `data:image/php` virava um `.php` executável.
     */
    public function storeBase64Image(string $base64): ?string
    {
        $binary = $this->decodeDataUri($base64);
        $info   = $binary === null ? false : @getimagesizefromstring($binary);
        $ext    = $info ? (self::IMAGE_EXTENSIONS[$info['mime']] ?? null) : null;

        if ($ext === null) {
            return null;
        }

        $path = 'service_images/'.Str::uuid().'.'.$ext;
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /**
     * Conteúdo binário do data URI, ou null se o cabeçalho não for de imagem
     * aceita, o base64 for inválido ou passar do limite.
     */
    private function decodeDataUri(string $dataUri): ?string
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $dataUri, $matches)) {
            return null;
        }

        $binary = base64_decode(substr($dataUri, strlen($matches[0])), true);

        if ($binary === false || $binary === '' || strlen($binary) > self::MAX_IMAGE_BYTES) {
            return null;
        }

        return $binary;
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
