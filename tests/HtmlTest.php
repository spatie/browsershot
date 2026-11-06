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
    $html = Browsershot::url(testUrl())
        ->bodyHtml();

    expect($html)->toContain('<h1>Example Domain</h1>');
});

it('can get the body html when using pipe', function () {
    $html = Browsershot::url(testUrl())
        ->usePipe()
        ->bodyHtml();

    expect($html)->toContain('<h1>Example Domain</h1>');
});

it('controls local scripts stylesheets and images without changing file input behavior', function (string $mode, bool $allowed) {
    $directory = __DIR__.'/temp';
    file_put_contents("{$directory}/local.js", 'window.localScriptLoaded = true;');
    file_put_contents("{$directory}/local.css", 'body { --local-stylesheet: loaded; }');
    file_put_contents("{$directory}/local.svg", '<svg xmlns="http://www.w3.org/2000/svg" width="37" height="19"></svg>');

    $html = <<<HTML
        <link rel="stylesheet" href="{$directory}/local.css">
        <script src="{$directory}/local.js"></script>
        <img id="local" src="{$directory}/local.svg">
        HTML;

    $browsershot = Browsershot::html($html);

    if ($mode === 'allowed' || $mode === 'reset') {
        $browsershot->allowLocalFileAccess();
    }

    if ($mode === 'reset') {
        $browsershot->allowLocalFileAccess(false);
    }

    if ($mode === 'file input') {
        file_put_contents("{$directory}/local.html", $html);
        $browsershot->setHtmlFromFilePath("{$directory}/local.html");
    }

    $result = json_decode($browsershot->evaluate('JSON.stringify({
        script: window.localScriptLoaded === true,
        stylesheet: getComputedStyle(document.body).getPropertyValue("--local-stylesheet").trim(),
        imageWidth: document.getElementById("local").naturalWidth
    })'), true);

    expect($result)->toBe([
        'script' => $allowed,
        'stylesheet' => $allowed ? 'loaded' : '',
        'imageWidth' => $allowed ? 37 : 0,
    ]);
})->with([
    'blocked by default' => ['default', false],
    'explicit opt-in' => ['allowed', true],
    'opt-in revoked' => ['reset', false],
    'explicit file input' => ['file input', true],
]);

it('still loads http resources with a content url when local files are blocked', function () {
    $html = Browsershot::html('<iframe src="/"></iframe>')
        ->setContentUrl(testUrl('document'))
        ->evaluate('document.querySelector("iframe").contentDocument.body.innerText');

    expect($html)->toContain('Example Domain');
});
