<?php

return [
    'name'           => 'Empreende Vitória',
    'slug'           => 'empreendevitoria',

    // Conexão de banco usada por este site. Hoje aponta para a conexão
    // 'mysql' padrão (mesmo banco local). Quando houver um segundo site
    // dinâmico com banco próprio, criar uma conexão nomeada em
    // config/database.php e referenciá-la aqui.
    'db_connection'  => 'mysql',

    // Namespace de views: view('empreende::portal.index')
    'view_namespace' => 'empreende',

    // Cookie de sessão isolado por site (login num site não vale em outro).
    // Renomeado de 'empreende_session' para evitar conflito com cookies antigos
    // que ficaram gravados no navegador com Path=/empreendevitoria (antes do
    // session.path passar para '/'). Cookies de nome diferente são ignorados,
    // então o novo é setado limpo em Path=/ sem ambiguidade.
    'session_cookie' => 'empreendevitoria_session',

    // Host base do site, SEM o slug. O slug é acrescentado pelo prefixo das
    // rotas; por isso o root URL não deve incluí-lo (senão duplica:
    // /empreendevitoria/empreendevitoria/...). Os assets estáticos, servidos
    // direto pelo Apache em /www/{slug}/assets, usam host + slug (asset_url,
    // derivado no middleware).
    'app_url'        => env('EMPREENDE_APP_URL', 'https://prefeituradavitoria.pe.gov.br'),

    'mail'           => [
        'from_address' => 'no-reply@empreendevitoria.pe.gov.br',
        'from_name'    => 'Empreende Vitória',
    ],

    // Caminho relativo de storage do site (uploads, etc.).
    'storage_path'   => 'sites/empreendevitoria',

    // false = site dinâmico (tem banco/controllers); true = só views estáticas.
    'static'         => false,
];
