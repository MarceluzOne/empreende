<?php

/*
|--------------------------------------------------------------------------
| Site de TESTE (validação em produção antes do switchover)
|--------------------------------------------------------------------------
|
| Espelha o site 'empreendevitoria' (mesmas views, mesmo banco, mesmas rotas),
| porém respondendo no slug /empreende-teste. Serve para validar a nova
| plataforma multi-site no servidor real SEM tocar no /empreendevitoria/ que
| está no ar.
|
| ATENÇÃO: o design atual suporta UM site dinâmico ativo por vez (os nomes de
| rota não são namespaced por site). Portanto, no servidor, a pasta
| config/sites/ deve conter SOMENTE este arquivo durante o teste; e SOMENTE
| empreendevitoria.php em produção. Os dois nunca devem coexistir.
|
| Onde subir: /laravel/config/sites/empreende-teste.php
| Depois de validar, remova este arquivo (e a pasta /www/empreende-teste/).
*/

return [
    'name'           => 'Empreende Vitória (TESTE)',
    'slug'           => 'empreende-teste',

    // Mesmo banco do empreende (validação usa os dados reais de produção).
    'db_connection'  => 'mysql',

    // Reaproveita exatamente as mesmas views do empreende.
    'view_namespace' => 'empreende',

    // Cookie próprio, para não conflitar com a sessão do site real.
    'session_cookie' => 'empreendeteste_session',

    // Host base SEM slug. O middleware deriva asset_url = host + '/empreende-teste',
    // então os assets estáticos devem ficar em /www/empreende-teste/assets.
    'app_url'        => env('EMPREENDE_APP_URL', 'https://prefeituradavitoria.pe.gov.br'),

    'mail'           => [
        'from_address' => 'no-reply@empreendevitoria.pe.gov.br',
        'from_name'    => 'Empreende Vitória (TESTE)',
    ],

    'storage_path'   => 'sites/empreendevitoria',

    'static'         => false,
];
