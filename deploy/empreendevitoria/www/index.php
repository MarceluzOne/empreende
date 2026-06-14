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

require __DIR__ . '/../../laravel/public/index.php';
