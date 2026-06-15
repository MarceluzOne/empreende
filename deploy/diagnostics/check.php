<?php

/*
 * Diagnóstico do boot do Laravel reproduzindo uma REQUISIÇÃO REAL.
 *
 * Onde subir: /www/empreende-teste/check.php
 * Acessar:    https://prefeituradavitoria.pe.gov.br/empreende-teste/check.php
 *
 * Roda o mesmo fluxo do site (bootstrap + middleware + rota '/') porém SEM o
 * try/catch do kernel, para que a exceção real apareça na tela.
 * APAGAR depois do diagnóstico.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

// Mesmo slug que o proxy index.php define.
define('SITE_SLUG', 'empreende-teste');

$laravel = __DIR__ . '/../../laravel';

echo "PHP " . PHP_VERSION . "\n";
echo "Laravel base: $laravel\n\n";

require $laravel . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $laravel . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Requisição falsa apontando para a home do site de teste.
$request = \Illuminate\Http\Request::create('/empreende-teste/', 'GET');

try {
    // sendRequestThroughRouter() faz bootstrap + pipeline + rota e LANÇA a
    // exceção (o try/catch fica no handle() público, que não usamos aqui).
    $ref = new \ReflectionMethod($kernel, 'sendRequestThroughRouter');
    $ref->setAccessible(true);
    $response = $ref->invoke($kernel, $request);

    echo "=== BOOT + ROTA OK ===\n";
    echo "STATUS HTTP: " . $response->getStatusCode() . "\n\n";

    echo "--- diagnostico de URLs/assets ---\n";
    echo "environment:      " . app()->environment() . "\n";
    echo "config app.url:   " . config('app.url') . "\n";
    echo "config asset_url: " . (config('app.asset_url') ?: '(vazio)') . "\n";
    echo "env EMPREENDE_APP_URL: " . (env('EMPREENDE_APP_URL') ?: '(vazio)') . "\n";
    echo "env ASSET_URL:    " . (env('ASSET_URL') ?: '(vazio)') . "\n";
    echo "asset('assets/x.png'): " . asset('assets/x.png') . "\n";
    echo "url('/teste'):    " . url('/teste') . "\n";
    try {
        echo "route('home'):    " . route('home') . "\n";
    } catch (\Throwable $e) {
        echo "route('home'):    ERRO - " . $e->getMessage() . "\n";
    }

    if ($response->getStatusCode() >= 400) {
        echo "\n--- inicio do conteudo da resposta ---\n";
        echo substr(strip_tags($response->getContent()), 0, 1500) . "\n";
    }
} catch (\Throwable $e) {
    echo "=== EXCECAO REAL ===\n";
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo "em " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "--- stack trace ---\n";
    echo $e->getTraceAsString() . "\n";
}
