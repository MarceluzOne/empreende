<?php
  header('Content-Type: text/plain; charset=utf-8');

  echo "=== DIAGNOSTICO ===\n";
  echo "DIR atual:        " . __DIR__ . "\n";
  echo "Document root:    " . ($_SERVER['DOCUMENT_ROOT'] ?? 'n/d') . "\n";
  echo "open_basedir:     " . (ini_get('open_basedir') ?: '(sem restricao)') . "\n";
  echo "PHP version:      " . PHP_VERSION . "\n\n";

  // tenta achar a pasta laravel-teste subindo niveis
  $alvos = [
      __DIR__ . '/../../laravel-teste/ping.php',
      __DIR__ . '/../../../laravel-teste/ping.php',
      __DIR__ . '/../laravel-teste/ping.php',
  ];

  foreach ($alvos as $caminho) {
      echo "Testando: $caminho\n";
      if (file_exists($caminho)) {
          echo "  -> EXISTE e ";
          echo is_readable($caminho) ? "LEGIVEL ✓ (caminho bom!)\n" : "NAO legivel ✗\n";
      } else {
          echo "  -> nao encontrado\n";
      }
  }