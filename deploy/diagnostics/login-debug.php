<?php

/*
 * Diagnóstico de SESSÃO/LOGIN no servidor real.
 *
 * Onde subir: /www/empreendevitoria/login-debug.php
 * Acessar:    https://prefeituradavitoria.pe.gov.br/empreendevitoria/login-debug.php
 *
 * Roda uma requisição REAL (kernel completo => todos os middlewares rodam) para
 * a página de login e dumpa: ambiente, config efetiva de sessão, os Set-Cookie
 * de fato emitidos, o banco em uso e se a tabela `sessions` existe.
 * NÃO faz login — só inspeciona o que o servidor está produzindo.
 *
 * APAGAR depois do diagnóstico.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

define('SITE_SLUG', 'empreendevitoria');

$laravel = __DIR__ . '/../../laravel';

echo "PHP " . PHP_VERSION . "\n";
echo "Laravel base: $laravel\n\n";

require $laravel . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $laravel . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Requisição REAL (https) para a página de login do site.
$request = \Illuminate\Http\Request::create(
    'https://prefeituradavitoria.pe.gov.br/empreendevitoria/entrar',
    'GET'
);

$response = $kernel->handle($request);

echo "=== AMBIENTE / URLS ===\n";
echo "environment        : " . app()->environment() . "\n";
echo "STATUS página login: " . $response->getStatusCode() . "\n";
echo "config app.url     : " . config('app.url') . "\n";
echo "config app.asset_url: " . (config('app.asset_url') ?: '(vazio)') . "\n";
try { echo "route('login')     : " . route('login') . "\n"; } catch (\Throwable $e) { echo "route('login')     : ERRO " . $e->getMessage() . "\n"; }
try { echo "route('panel')     : " . route('panel') . "\n"; } catch (\Throwable $e) { echo "route('panel')     : ERRO " . $e->getMessage() . "\n"; }

echo "\n=== SESSION CONFIG (efetiva, pós-middleware) ===\n";
foreach (['driver', 'connection', 'table', 'cookie', 'path', 'domain', 'secure', 'same_site', 'http_only', 'lifetime', 'encrypt'] as $k) {
    echo str_pad("session.$k", 22) . ": " . var_export(config("session.$k"), true) . "\n";
}
echo str_pad("db.default", 22) . ": " . config('database.default') . "\n";
echo str_pad("db name", 22) . ": " . config('database.connections.' . config('database.default') . '.database') . "\n";
echo str_pad("db host", 22) . ": " . config('database.connections.' . config('database.default') . '.host') . "\n";

echo "\n=== SET-COOKIE REALMENTE EMITIDOS ===\n";
$cookies = $response->headers->getCookies();
if (!$cookies) {
    echo "(nenhum cookie emitido!)\n";
}
foreach ($cookies as $c) {
    echo "- " . $c->getName()
        . " | path=" . $c->getPath()
        . " | domain=" . var_export($c->getDomain(), true)
        . " | secure=" . var_export($c->isSecure(), true)
        . " | httponly=" . var_export($c->isHttpOnly(), true)
        . " | samesite=" . var_export($c->getSameSite(), true)
        . "\n";
}

echo "\n=== TABELA sessions ===\n";
try {
    if (config('session.driver') === 'database') {
        $conn = config('session.connection') ?: config('database.default');
        $table = config('session.table');
        $exists = \Illuminate\Support\Facades\Schema::connection($conn)->hasTable($table);
        echo "driver=database | conexão=$conn | tabela '$table' existe? " . ($exists ? 'SIM' : 'NÃO') . "\n";
        if ($exists) {
            $cols = \Illuminate\Support\Facades\Schema::connection($conn)->getColumnListing($table);
            echo "colunas: " . implode(', ', $cols) . "\n";
            $count = \Illuminate\Support\Facades\DB::connection($conn)->table($table)->count();
            echo "linhas na tabela: $count\n";
        }
    } else {
        echo "driver=" . config('session.driver') . " (não usa tabela)\n";
        $p = storage_path('framework/sessions');
        echo "dir: $p\n";
        echo "existe=" . (is_dir($p) ? 'sim' : 'NÃO') . " | gravável=" . (is_writable($p) ? 'sim' : 'NÃO') . "\n";
    }
} catch (\Throwable $e) {
    echo "ERRO ao checar sessions: " . get_class($e) . " - " . $e->getMessage() . "\n";
}

$kernel->terminate($request, $response);
