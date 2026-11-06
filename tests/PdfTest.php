<?php

use Spatie\Browsershot\Browsershot;
use Spatie\PdfToText\Pdf;
use Symfony\Component\Process\Process;

it('can save a pdf by using the pdf extension', function () {
    $targetPath = __DIR__.'/temp/testPdf.pdf';

    Browsershot::url(testUrl())
        ->save($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can save a highly customized pdf', function () {
    $targetPath = __DIR__.'/temp/customPdf.pdf';

    Browsershot::url(testUrl())
        ->hideBrowserHeaderAndFooter()
        ->showBackground()
        ->landscape()
        ->margins(5, 25, 5, 25)
        ->pages('1')
        ->savePdf($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can save a highly customized pdf when using pipe', function () {
    $targetPath = __DIR__.'/temp/customPdf.pdf';

    Browsershot::url(testUrl())
        ->usePipe()
        ->hideBrowserHeaderAndFooter()
        ->showBackground()
        ->landscape()
        ->margins(5, 25, 5, 25)
        ->pages('1')
        ->savePdf($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can save a pdf with the taggedPdf option', function () {
    $targetPath = __DIR__.'/temp/customPdf.pdf';

    Browsershot::url(testUrl())
        ->taggedPdf()
        ->pages('1')
        ->savePdf($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can return a pdf as base 64', function () {
    $base64 = Browsershot::url(testUrl())
        ->base64pdf();

    expect(is_string($base64))->toBeTrue();
});

it('can return a pdf', function () {
    $binPath = PHP_OS === 'Linux' ? '/usr/bin/pdftotext' : '/opt/homebrew/bin/pdftotext';
    $targetPath = __DIR__.'/temp/testPdf.pdf';

    $pdf = Browsershot::url(testUrl())
        ->pdf();
    file_put_contents($targetPath, $pdf);

    expect(Pdf::getText($targetPath, $binPath))->toContain('Example Domain');
});

it('renders file urls as plain text without allowing local file access', function () {
    $targetPath = __DIR__.'/temp/plain-text-file-urls.pdf';

    Browsershot::html('<p>file: invoice.pdf</p><p>file:/var/data/report.txt</p><p>file:///var/data/report.txt</p>')
        ->savePdf($targetPath);

    $binPath = PHP_OS === 'Linux' ? '/usr/bin/pdftotext' : '/opt/homebrew/bin/pdftotext';

    expect(Pdf::getText($targetPath, $binPath))
        ->toContain('file: invoice.pdf')
        ->toContain('file:/var/data/report.txt')
        ->toContain('file:///var/data/report.txt');
});

it('only renders local files in supplied html when explicitly allowed', function (string $reference, bool $javascript, bool $allow) {
    $secretPath = __DIR__.'/temp/local-file-secret.txt';
    $targetPath = __DIR__.'/temp/local-file-access.pdf';
    $secret = 'LOCAL-FILE-SECRET-7731';

    file_put_contents($secretPath, $secret);

    $html = str_replace('{path}', $secretPath, $reference);
    $browsershot = Browsershot::html('<p>Benign content</p>'.$html);

    if (! $javascript) {
        $browsershot->disableJavascript();
    }

    if ($allow) {
        $browsershot->allowLocalFileAccess();
    }

    $browsershot->savePdf($targetPath);

    $binPath = PHP_OS === 'Linux' ? '/usr/bin/pdftotext' : '/opt/homebrew/bin/pdftotext';
    $text = Pdf::getText($targetPath, $binPath);

    expect($text)->toContain('Benign content');
    expect(str_contains($text, $secret))->toBe($allow);
})->with([
    'root-relative' => ['<iframe src="{path}"></iframe>', true],
    'root-relative without javascript' => ['<iframe src="{path}"></iframe>', false],
    'javascript-generated' => ['<iframe id="local"></iframe><script>document.getElementById("local").src = "fi" + "le://{path}";</script>', true],
    'literal file url' => ['<iframe src="file://{path}"></iframe>', true],
    'single-slash file url' => ['<iframe src="file:{path}"></iframe>', true],
    'entity-encoded file url' => ['<iframe src="&#102;ile://{path}"></iframe>', true],
])->with([false, true]);

it('does not reuse the initial html document permission for subframes', function (bool $allow) {
    $targetPath = __DIR__.'/temp/repeated-document.pdf';
    $browsershot = Browsershot::html('<p>DOCUMENT-MARKER</p><script>
        if (window === top) {
            const frame = document.createElement("iframe");
            frame.src = location.href;
            document.body.append(frame);
        }
    </script>');

    if ($allow) {
        $browsershot->allowLocalFileAccess();
    }

    $browsershot->savePdf($targetPath);

    $binPath = PHP_OS === 'Linux' ? '/usr/bin/pdftotext' : '/opt/homebrew/bin/pdftotext';

    expect(substr_count(Pdf::getText($targetPath, $binPath), 'DOCUMENT-MARKER'))->toBe($allow ? 2 : 1);
})->with([false, true]);

it('can load puppeteer when require(esm) is disabled', function () {
    $binPath = PHP_OS === 'Linux' ? '/usr/bin/pdftotext' : '/opt/homebrew/bin/pdftotext';
    $targetPath = __DIR__.'/temp/esmFallback.pdf';

    Browsershot::html('<p>ESM fallback</p><script>document.body.append(navigator.userAgent)</script>')
        ->setNodeEnv(['NODE_OPTIONS' => '--no-experimental-require-module'])
        ->device('iPhone X')
        ->savePdf($targetPath);

    expect(Pdf::getText($targetPath, $binPath))
        ->toContain('ESM fallback')
        ->toContain('iPhone');
})->skip(function () {
    $process = new Process(['node', '-e', ''], null, ['NODE_OPTIONS' => '--no-experimental-require-module']);
    $process->run();

    return ! $process->isSuccessful();
}, 'This Node version does not accept --no-experimental-require-module in NODE_OPTIONS');

it('can write options to a file and generate a pdf', function () {
    $targetPath = __DIR__.'/temp/testPdf.pdf';

    Browsershot::url(testUrl())
        ->writeOptionsToFile()
        ->save($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can generate a pdf with custom paper size unit', function () {
    $command = Browsershot::url('https://example.com')
        ->paperSize(8.3, 11.7, 'in')
        ->margins(0.39, 0.78, 1.18, 1.57, 'in')
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'margin' => [
                'top' => '0.39in',
                'right' => '0.78in',
                'bottom' => '1.18in',
                'left' => '1.57in',
            ],
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'width' => '8.3in',
            'height' => '11.7in',
            'args' => [],
        ],
    ], $command);
});
