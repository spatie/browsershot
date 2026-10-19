<?php

use Symfony\Component\Process\Process;

expect()->extend('toHaveMime', function (string $expectedMimeType) {
    $actualMimeType = mime_content_type($this->value);

    expect($actualMimeType)->toBe($expectedMimeType);
});

function testUrl(string $path = ''): string
{
    static $server;
    static $url;

    if (! $server) {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($socket, false);
        fclose($socket);

        $url = "http://{$address}";
        $server = new Process([PHP_BINARY, '-S', $address, __DIR__.'/fixtures/router.php']);
        $server->start();

        register_shutdown_function(fn () => $server->stop());

        $server->waitUntil(fn (string $type, string $output) => str_contains($output, 'Development Server'));
    }

    return $url.'/'.ltrim($path, '/');
}

function emptyTempDirectory(): void
{
    $tempDirPath = __DIR__.'/temp';

    $files = scandir($tempDirPath);

    foreach ($files as $file) {
        if (! in_array($file, ['.', '..', '.gitignore'])) {
            unlink("{$tempDirPath}/{$file}");
        }
    }
}
