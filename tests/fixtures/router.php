<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/redirect') {
    header('Location: /', true, 302);

    return;
}

if ($path === '/post') {
    header('Content-Type: application/json');
    echo json_encode($_POST, JSON_PRETTY_PRINT);

    return;
}

if ($path === '/broken-image') {
    echo '<html><head><link rel="icon" href="data:,"></head><body><img src="/missing.jpg"><script>console.log("Fixture message")</script></body></html>';

    return;
}

if ($path !== '/') {
    http_response_code(404);
    echo 'Not Found';

    return;
}

echo '<!DOCTYPE html><html><head><title>Example Domain</title><link rel="icon" href="data:,"></head><body><div><h1>Example Domain</h1><p>A local page for browser tests.</p></div></body></html>';
