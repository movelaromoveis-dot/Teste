<?php
/**
 * Script para criar ZIP correto do plugin
 * Uso: php create-zip.php
 */

$sourceDir = __DIR__;
$zipName = dirname($sourceDir) . DIRECTORY_SEPARATOR . 'wp-product-ai-summaries.zip';

// Remover ZIP anterior se existir
if (file_exists($zipName)) {
    unlink($zipName);
}

$zip = new ZipArchive();
if ($zip->open($zipName, ZipArchive::CREATE) === true) {
    $files = array(
        'wp-product-ai-summaries.php',
        'admin.js',
        'admin-settings.css',
        'admin-metabox.css',
        'README.md',
    );
    
    $folderName = 'wp-product-ai-summaries';
    
    foreach ($files as $file) {
        $filePath = $sourceDir . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filePath)) {
            $zip->addFile($filePath, $folderName . '/' . $file);
            echo "✓ Adicionado: $file\n";
        }
    }
    
    $zip->close();
    
    $size = filesize($zipName);
    echo "\n✓ ZIP criado com sucesso!\n";
    echo "Arquivo: " . basename($zipName) . "\n";
    echo "Tamanho: " . round($size / 1024, 2) . " KB\n";
    echo "Caminho: " . $zipName . "\n";
    echo "\nO plugin está pronto para instalar no WordPress!\n";
} else {
    echo "✗ Erro ao criar ZIP\n";
}
?>
