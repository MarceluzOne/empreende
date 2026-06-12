<?php

// Limpa cache do Laravel manualmente
$basePath = dirname(__DIR__);

$dirs = [
    $basePath . '/bootstrap/cache',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/framework/sessions',
];

foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                unlink($file);
            }
        }
    }
}

echo "Cache limpo com sucesso!";
