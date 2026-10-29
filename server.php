<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

// The project root is the web root (cPanel layout), so views reference assets
// as asset('public/...'). Serve those from the project folder, limited to /public/.
if (strpos($uri, '/public/') === 0 && strpos($uri, '..') === false && is_file(__DIR__.$uri)) {
    $mimeTypes = [
        'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json',
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif',
        'svg' => 'image/svg+xml', 'webp' => 'image/webp', 'ico' => 'image/x-icon',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'eot' => 'application/vnd.ms-fontobject',
        'pdf' => 'application/pdf', 'mp4' => 'video/mp4',
    ];
    $extension = strtolower(pathinfo($uri, PATHINFO_EXTENSION));
    header('Content-Type: '.($mimeTypes[$extension] ?? mime_content_type(__DIR__.$uri)));
    header('Content-Length: '.filesize(__DIR__.$uri));
    readfile(__DIR__.$uri);
    return true;
}

require_once __DIR__.'/index.php';
