<?php

$models_dir = __DIR__ . '/app/Models';

function remove_incorrect_db_import($filepath) {
    $content = file_get_contents($filepath);
    
    $incorrect_import = 'use function CodeIgniter\\Database\\db;';
    
    if (strpos($content, $incorrect_import) === false) {
        echo "SKIP: $filepath (pas d'import incorrect à supprimer)\n";
        return false;
    }
    
    $content = str_replace($incorrect_import . "\n", '', $content);
    $content = str_replace($incorrect_import, '', $content);
    
    file_put_contents($filepath, $content);
    echo "FIXED: $filepath (import incorrect supprimé)\n";
    return true;
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($models_dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$fixed_count = 0;
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        if (remove_incorrect_db_import($file->getPathname())) {
            $fixed_count++;
        }
    }
}

echo "\nTotal de fichiers corrigés: $fixed_count\n";
