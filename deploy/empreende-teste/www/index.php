<?php

/*
 * Proxy fino do site de TESTE.
 *
 * Onde subir: /www/empreende-teste/index.php
 *
 * Define o slug do site e delega para o front controller do Laravel único,
 * que vive ACIMA do docroot em /home/<conta>/laravel/ (validado: o caminho
 * relativo ../../laravel/ a partir de /www/empreende-teste/ resolve correto).
 */

define('SITE_SLUG', 'empreende-teste');

require __DIR__ . '/../../laravel/public/index.php';
