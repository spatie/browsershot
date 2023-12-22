<?php

use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\FileDoesNotExistException;

it('can set html contents from a file', function () {
    $inputFile = __DIR__.'/temp/test.html';
    $inputHtml = '<html><head></head><body><h1>Hello World</h1></body></html>';

    file_put_contents($inputFile, $inputHtml);

    $outputHtml = Browsershot::htmlFromFilePath($inputFile)
        ->usePipe()
        ->bodyHtml();

    expect($outputHtml)->toEqual($inputHtml);
});

it('can not set html contents from a non-existent file', function () {
    Browsershot::htmlFromFilePath(__DIR__.'/temp/non-existent-file.html');
})->throws(FileDoesNotExistException::class);

it('can get the body html', function () {
    $html = Browsershot::url('https://example.com')
        ->bodyHtml();

    expect($html)->toContain('<h1>Example Domain</h1>');
});

it('can get the body html when using pipe', function () {
    $html = Browsershot::url('https://example.com')
        ->usePipe()
        ->bodyHtml();

    expect($html)->toContain('<h1>Example Domain</h1>');
});
