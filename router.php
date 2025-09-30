<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri === '/' || $uri === '') {
    include __DIR__ . '/frontend/index.html';
} elseif (file_exists(__DIR__ . $uri)) {
    return false; // serve static files
} elseif (file_exists(__DIR__ . $uri)) {
    include __DIR__ . $uri;
} else {
    http_response_code(404);
    echo "File not found: $uri";
}
