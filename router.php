<?php
// router.php: Serves frontend and proxies API calls

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing files as-is
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Serve backend API
if (strpos($uri, '/api/') === 0) {
    require __DIR__ . '/backend' . $uri;
} else {
    // Serve frontend index.html for all other routes
    require __DIR__ . '/frontend/index.html';
}
