<?php

/*
 * Proxy fino do site REAL (usado no SWITCHOVER — Fase 3).
 *
 * Onde subir: /www/empreendevitoria/index.php
 * (substitui o Laravel antigo que está nessa pasta hoje)
 *
 * Só subir DEPOIS de validado o /empreende-teste/ e com backup feito.
 */

define('SITE_SLUG', 'empreendevitoria');

/*
 * Ver explicação no proxy do site de teste: força o Laravel a enxergar o
 * caminho COMPLETO (com o slug), já que as rotas são registradas com o prefixo
 * do slug e a pasta proxy tem o mesmo nome.
 */
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/../../laravel/public/index.php';
