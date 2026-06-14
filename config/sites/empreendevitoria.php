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
    'session_cookie' => 'empreende_session',

    // URL pública do site (com prefixo do slug).
    'app_url'        => env('EMPREENDE_APP_URL', 'https://prefeituradavitoria.pe.gov.br/empreendevitoria'),

    'mail'           => [
        'from_address' => 'no-reply@empreendevitoria.pe.gov.br',
        'from_name'    => 'Empreende Vitória',
    ],

    // Caminho relativo de storage do site (uploads, etc.).
    'storage_path'   => 'sites/empreendevitoria',

    // false = site dinâmico (tem banco/controllers); true = só views estáticas.
    'static'         => false,
];
