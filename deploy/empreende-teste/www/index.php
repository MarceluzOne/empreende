<?php

/*
 * Proxy fino do site de TESTE.
 *
 * Onde subir: /www/empreende-teste/index.php
 *
 * Define o slug do site e delega para o front controller do Laravel único,
 * que vive ACIMA do docroot em /home/<conta>/laravel/.
 */

define('SITE_SLUG', 'empreende-teste');

/*
 * As rotas são registradas COM o prefixo do slug (ex.: /empreende-teste/entrar).
 * Como esta pasta proxy está numa subpasta de mesmo nome, por padrão o Symfony
 * removeria "/empreende-teste" do caminho (baseUrl), e as rotas prefixadas não
 * casariam (a raiz viraria "/" e cairia no redirect do site padrão; as demais
 * dariam 404).
 *
 * Forçando SCRIPT_NAME/PHP_SELF para "/index.php", o Laravel passa a enxergar o
 * caminho COMPLETO (com o slug), e as rotas batem corretamente.
 */
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/../../laravel/public/index.php';
