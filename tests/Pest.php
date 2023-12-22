<?php

expect()->extend('toHaveMime', function (string $expectedMimeType) {
    $actualMimeType = mime_content_type($this->value);

    expect($actualMimeType)->toBe($expectedMimeType);
});

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
