<?php

/**
 * Laravel Router for PHP Built-in Web Server
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$publicFile = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    $mimeTypes = [
        'css' => 'text/css',
        'js'  => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg'=> 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp'=> 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'json' => 'application/json',
    ];

    $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        header("Content-Type: " . $mimeTypes[$ext]);
    } else {
        $mime = mime_content_type($publicFile);
        if ($mime) {
            header("Content-Type: " . $mime);
        }
    }
    header("Content-Length: " . filesize($publicFile));
    readfile($publicFile);
    exit;
}

require_once __DIR__ . '/public/index.php';
