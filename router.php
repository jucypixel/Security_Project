<?php

$request = strtok($_SERVER['REQUEST_URI'], '?'); 
$root = __DIR__;
$file = $root . $request;

// CHECK IF REQUEST CAME FROM YOUR SITE (not typed manually)
$internalAsset = (
    strpos($_SERVER['HTTP_REFERER'] ?? '', 'localhost:8000') !== false
);

// --- Handle CSS, JS, images ---
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg)$/i', $request)) {

    // If user opens link directly → BLOCK
    if (!$internalAsset) {
        http_response_code(404);
        require $root . "/404.php";   // custom page
        exit;
    }

    // Allow internal loading
    return false;
}

// --- Handle PHP files ---
if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {

    // If accessed from "view-source:" → BLOCK
    if (isset($_SERVER['HTTP_REFERER']) && 
        str_starts_with($_SERVER['HTTP_REFERER'], "view-source:")) 
    {
        http_response_code(404);
        require $root . "/404.php";   // custom page
        exit;
    }

    require $file;
    exit;
}

// --- Default route: if no match → show custom 404 ---
http_response_code(404);
require $root . "/404.php";
exit;

