<?php

use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Spatie\Browsershot\Exceptions\ElementNotFound;
use Spatie\Browsershot\Exceptions\UnsuccessfulResponse;
use Spatie\Image\Manipulations;
use Symfony\Component\Process\Exception\ProcessFailedException;

beforeEach(function () {
    $this->emptyTempDirectory();
});

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

it('can get the requests list', function () {
    $list = Browsershot::url('https://example.com')
        ->triggeredRequests();

    expect($list)->toHaveCount(1);

    $this->assertEquals(
        [['url' => 'https://example.com/']],
        $list
    );
});

it('can take a screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::url('https://example.com')
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can return a screenshot as base 64', function () {
    $base64 = Browsershot::url('https://example.com')
        ->base64Screenshot();

    expect(is_string($base64))->toBeTrue();
});

it('can take a screenshot when using pipe', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::url('https://example.com')
        ->usePipe()
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can take a screenshot of arbitrary html', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::html('<h1>Hello world!!</h1>')
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can take a high density screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::url('https://example.com')
        ->deviceScaleFactor(2)
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('cannot save without an extension', function () {
    $this->expectException(CouldNotTakeBrowsershot::class);

    $targetPath = __DIR__.'/temp/testScreenshot';

    Browsershot::url('https://example.com')
        ->save($targetPath);
});

it('can take a full page screenshot', function () {
    $targetPath = __DIR__.'/temp/fullpageScreenshot.png';

    Browsershot::url('https://github.com/spatie/browsershot')
        ->fullPage()
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can take a highly customized screenshot', function () {
    $targetPath = __DIR__.'/temp/customScreenshot.png';

    Browsershot::url('https://example.com')
        ->clip(290, 80, 700, 290)
        ->deviceScaleFactor(2)
        ->dismissDialogs()
        ->mobile()
        ->touch()
        ->windowSize(1280, 800)
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can take a screenshot of an element matching a selector', function () {
    $targetPath = __DIR__.'/temp/nodeScreenshot.png';

    Browsershot::url('https://example.com')
        ->select('div')
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('throws an exception if the selector does not match any elements', function () {
    $this->expectException(ElementNotFound::class);
    $targetPath = __DIR__.'/temp/nodeScreenshot.png';

    Browsershot::url('https://example.com')
        ->select('not-a-valid-selector')
        ->save($targetPath);
});

it('can save a pdf by using the pdf extension', function () {
    $targetPath = __DIR__.'/temp/testPdf.pdf';

    Browsershot::url('https://example.com')
        ->save($targetPath);

    expect($targetPath)->toBeFile();

    expect(mime_content_type($targetPath))->toEqual('application/pdf');
});

it('can save a highly customized pdf', function () {
    $targetPath = __DIR__.'/temp/customPdf.pdf';

    Browsershot::url('https://example.com')
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

    Browsershot::url('https://example.com')
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

it('can return a pdf as base 64', function () {
    $base64 = Browsershot::url('https://example.com')
        ->base64pdf();

    expect(is_string($base64))->toBeTrue();
});

it('can handle a permissions error', function () {
    $targetPath = '/cantWriteThisPdf.png';

    $this->expectException(ProcessFailedException::class);

    Browsershot::url('https://example.com')
        ->save($targetPath);
});

it('can create a command to generate a screenshot', function () {
    $command = Browsershot::url('https://example.com')
        ->clip(100, 50, 600, 400)
        ->deviceScaleFactor(2)
        ->fullPage()
        ->dismissDialogs()
        ->windowSize(1920, 1080)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'clip' => ['x' => 100, 'y' => 50, 'width' => 600, 'height' => 400],
            'path' => 'screenshot.png',
            'fullPage' => true,
            'dismissDialogs' => true,
            'viewport' => [
                'deviceScaleFactor' => 2,
                'width' => 1920,
                'height' => 1080,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can create a command to generate a screenshot and omit the background', function () {
    $command = Browsershot::url('https://example.com')
        ->clip(100, 50, 600, 400)
        ->deviceScaleFactor(2)
        ->hideBackground()
        ->windowSize(1920, 1080)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'clip' => ['x' => 100, 'y' => 50, 'width' => 600, 'height' => 400],
            'path' => 'screenshot.png',
            'omitBackground' => true,
            'viewport' => [
                'deviceScaleFactor' => 2,
                'width' => 1920,
                'height' => 1080,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can create a command to generate a pdf', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->transparentBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->paperSize(210, 148)
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'omitBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'width' => '210mm',
            'height' => '148mm',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can create a command to generate a pdf with a custom header', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->paperSize(210, 148)
        ->showBrowserHeaderAndFooter()
        ->headerHtml('<p>Test Header</p>')
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'width' => '210mm',
            'height' => '148mm',
            'displayHeaderFooter' => true,
            'headerTemplate' => '<p>Test Header</p>',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can create a command to generate a pdf with a custom footer', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->paperSize(210, 148)
        ->showBrowserHeaderAndFooter()
        ->footerHtml('<p>Test Footer</p>')
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'width' => '210mm',
            'height' => '148mm',
            'displayHeaderFooter' => true,
            'footerTemplate' => '<p>Test Footer</p>',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can create a command to generate a pdf with the header hidden', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->paperSize(210, 148)
        ->showBrowserHeaderAndFooter()
        ->hideHeader()
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'width' => '210mm',
            'height' => '148mm',
            'displayHeaderFooter' => true,
            'headerTemplate' => '<p></p>',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can create a command to generate a pdf with the footer hidden', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->paperSize(210, 148)
        ->showBrowserHeaderAndFooter()
        ->hideFooter()
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'width' => '210mm',
            'height' => '148mm',
            'displayHeaderFooter' => true,
            'footerTemplate' => '<p></p>',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can create a command to generate a pdf with paper format', function () {
    $command = Browsershot::url('https://example.com')
        ->showBackground()
        ->landscape()
        ->margins(10, 20, 30, 40)
        ->pages('1-3')
        ->format('a4')
        ->createPdfCommand('screenshot.pdf');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'pdf',
        'options' => [
            'path' => 'screenshot.pdf',
            'printBackground' => true,
            'landscape' => true,
            'margin' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
            'pageRanges' => '1-3',
            'format' => 'a4',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
        ],
    ], $command);
});

it('can use given user agent', function () {
    $command = Browsershot::url('https://example.com')
        ->userAgent('my_special_snowflake')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'userAgent' => 'my_special_snowflake',
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can set emulate media option', function () {
    $command = Browsershot::url('https://example.com')
        ->emulateMedia('screen')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'emulateMedia' => 'screen',
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can set emulate media option to null', function () {
    $command = Browsershot::url('https://example.com')
        ->emulateMedia(null)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'emulateMedia' => null,
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can use pipe', function () {
    $command = Browsershot::url('https://example.com')
        ->usePipe()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'pipe' => true,
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can set another node binary', function () {
    $this->expectException(ProcessFailedException::class);

    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::html('Foo')
        ->setBinPath(__DIR__.'/../browser.js')
        ->save($targetPath);
});

it('can set another chrome executable path', function () {
    $this->expectException(ProcessFailedException::class);

    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::html('Foo')
        ->setChromePath('non-existant/bin/wich/causes/an/exception')
        ->save($targetPath);
});

it('can set another bin path', function () {
    $this->expectException(ProcessFailedException::class);

    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::html('Foo')
            ->setBinPath('non-existant/bin/wich/causes/an/exception')
            ->save($targetPath);
});

it('can set the include path and still works', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::html('Foo')
        ->setIncludePath('$PATH:/usr/local/bin:/mypath')
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can run without sandbox', function () {
    $command = Browsershot::url('https://example.com')
        ->noSandbox()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [
                '--no-sandbox',
            ],
            'type' => 'png',
        ],
    ], $command);
});

it('can dismiss dialogs', function () {
    $command = Browsershot::url('https://example.com')
        ->dismissDialogs()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'dismissDialogs' => true,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can disable javascript', function () {
    $command = Browsershot::url('https://example.com')
        ->disableJavascript()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'disableJavascript' => true,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can disable images', function () {
    $command = Browsershot::url('https://example.com')
        ->disableImages()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'disableImages' => true,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can block urls', function () {
    $command = Browsershot::url('https://example.com')
        ->blockUrls([
            'example.com/cm-notify?pi=outbrain',
            'sync.outbrain.com/cookie-sync?p=bidswitch',
        ])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'blockUrls' => [
                'example.com/cm-notify?pi=outbrain',
                'sync.outbrain.com/cookie-sync?p=bidswitch',
            ],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can block domains', function () {
    $command = Browsershot::url('https://example.com')
        ->blockDomains([
            'googletagmanager.com',
            'google-analytics.com',
        ])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'blockDomains' => [
                'googletagmanager.com',
                'google-analytics.com',
            ],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can ignore https errors', function () {
    $command = Browsershot::url('https://example.com')
        ->ignoreHttpsErrors()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'ignoreHttpsErrors' => true,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can use a proxy server', function () {
    $command = Browsershot::url('https://example.com')
        ->setProxyServer('1.2.3.4:8080')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => ['--proxy-server=1.2.3.4:8080'],
            'type' => 'png',
        ],
    ], $command);
});

it('can set arbitrary options', function () {
    $command = Browsershot::url('https://example.com')
        ->setOption('foo.bar', 100)
        ->setOption('foo.bar', 150)
        ->setOption('foo.baz', 200)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'foo' => [
                'bar' => 150,
                'baz' => 200,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can add a delay before taking a screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    $delay = 2000;

    $start = round(microtime(true) * 1000);

    Browsershot::url('https://example.com')
        ->setDelay($delay)
        ->save($targetPath);

    $end = round(microtime(true) * 1000);

    expect($end - $start)->toBeGreaterThanOrEqual($delay);
});

it('can get the output of a screenshot', function () {
    $output = Browsershot::url('https://example.com')
        ->screenshot();

    $finfo = finfo_open();

    $mimeType = finfo_buffer($finfo, $output, FILEINFO_MIME_TYPE);

    expect('image/png')->toEqual($mimeType);
});

it('can get the output of a pdf', function () {
    $output = Browsershot::url('https://example.com')
        ->pdf();

    $finfo = finfo_open();

    $mimeType = finfo_buffer($finfo, $output, FILEINFO_MIME_TYPE);

    expect('application/pdf')->toEqual($mimeType);
});

it('can save to temp dir with background', function () {
    $targetPath = tempnam(sys_get_temp_dir(), 'bs_').'.jpg';
    Browsershot::url('https://example.com')
        ->background('white')
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can wait until network idle', function () {
    $command = Browsershot::url('https://example.com')
        ->waitUntilNetworkIdle()
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'waitUntil' => 'networkidle0',
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);

    $command = Browsershot::url('https://example.com')
        ->waitUntilNetworkIdle($strict = false)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'waitUntil' => 'networkidle2',
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can send extra http headers', function () {
    $command = Browsershot::url('https://example.com')
        ->setExtraHttpHeaders(['extra-http-header' => 'extra-http-header'])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'extraHTTPHeaders' => ['extra-http-header' => 'extra-http-header'],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can send extra navigation http headers', function () {
    $command = Browsershot::url('https://example.com')
        ->setExtraNavigationHttpHeaders(['extra-http-header' => 'extra-http-header'])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'extraNavigationHTTPHeaders' => ['extra-http-header' => 'extra-http-header'],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can authenticate', function () {
    $command = Browsershot::url('https://example.com')
        ->authenticate('username1', 'password1')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'authentication' => ['username' => 'username1', 'password' => 'password1'],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can send cookies', function () {
    $command = Browsershot::url('https://example.com')
        ->useCookies(['theme' => 'light', 'sessionToken' => 'abc123'])
        ->useCookies(['theme' => 'dark'], 'ui.example.com')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'cookies' => [
                [
                    'name' => 'theme',
                    'value' => 'light',
                    'domain' => 'example.com',
                ],
                [
                    'name' => 'sessionToken',
                    'value' => 'abc123',
                    'domain' => 'example.com',
                ],
                [
                    'name' => 'theme',
                    'value' => 'dark',
                    'domain' => 'ui.example.com',
                ],
            ],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can send post request', function () {
    $command = Browsershot::url('https://example.com')
        ->post(['foo' => 'bar'])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'postParams' => ['foo' => 'bar'],
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can click on the page', function () {
    $command = Browsershot::url('https://example.com')
        ->click('#selector1')
        ->click('#selector2', 'right', 2, 1)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'clicks' => [
                [
                    'selector' => '#selector1',
                    'button' => 'left',
                    'clickCount' => 1,
                    'delay' => 0,
                ],
                [
                    'selector' => '#selector2',
                    'button' => 'right',
                    'clickCount' => 2,
                    'delay' => 1,
                ],
            ],
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can set type of screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.jpg';

    Browsershot::url('https://example.com')
        ->setScreenshotType('jpeg')
        ->save($targetPath);

    expect($targetPath)->toBeFile();

    $this->assertMimeType('image/jpeg', $targetPath);
});

it('can evaluate page', function () {
    $result = Browsershot::url('https://example.com')
        ->evaluate('1 + 1');

    expect($result)->toEqual('2');
});

it('can add a timeout to puppeteer', function () {
    $command = Browsershot::url('https://example.com')
        ->timeout(123)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'timeout' => 123000,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can add a wait for function to puppeteer', function () {
    $command = Browsershot::url('https://example.com')
        ->waitForFunction('window.innerWidth < 100')
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'function' => 'window.innerWidth < 100',
            'functionPolling' => 'raf',
            'functionTimeout' => 0,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can input form fields and post and get the body html', function () {
    $delay = 2000;

    $command = Browsershot::url('http://example.com')
        ->type('#selector1', 'Hello, is it me you are looking for?')
        ->click('#selector2')
        ->setDelay($delay)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'http://example.com',
        'action' => 'screenshot',
        'options' => [
            'clicks' => [
                [
                    'selector' => '#selector2',
                    'button' => 'left',
                    'clickCount' => 1,
                    'delay' => 0,
                ],
            ],
            'types' => [
                [
                    'selector' => '#selector1',
                    'text' => 'Hello, is it me you are looking for?',
                    'delay' => 0,
                ],
            ],
            'delay' => 2000,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can input form fields and post to ssl and get the body html', function () {
    $delay = 2000;

    $command = Browsershot::url('https://example.com')
        ->type('#selector1', 'Hello, is it me you are looking for?')
        ->click('#selector2')
        ->setDelay($delay)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'clicks' => [
                [
                    'selector' => '#selector2',
                    'button' => 'left',
                    'clickCount' => 1,
                    'delay' => 0,
                ],
            ],
            'types' => [
                [
                    'selector' => '#selector1',
                    'text' => 'Hello, is it me you are looking for?',
                    'delay' => 0,
                ],
            ],
            'delay' => 2000,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can change select fields and post and get the body html', function () {
    $delay = 2000;

    $command = Browsershot::url('http://example.com')
        ->selectOption('#selector1', 'option_one')
        ->click('#selector2')
        ->setDelay($delay)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'http://example.com',
        'action' => 'screenshot',
        'options' => [
            'clicks' => [
                [
                    'selector' => '#selector2',
                    'button' => 'left',
                    'clickCount' => 1,
                    'delay' => 0,
                ],
            ],
            'selects' => [
                [
                    'selector' => '#selector1',
                    'value' => 'option_one',
                ],
            ],
            'delay' => 2000,
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
        ],
    ], $command);
});

it('can write options to a file and generate a screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    Browsershot::url('https://example.com')
        ->writeOptionsToFile()
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can write options to a file and generate a pdf', function () {
    $targetPath = __DIR__.'/temp/testPdf.pdf';

    Browsershot::url('https://example.com')
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

it('will auto prefix chromium arguments', function () {
    $command = Browsershot::url('https://example.com')
        ->addChromiumArguments(['please-autoprefix-me'])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [
                '--please-autoprefix-me',
            ],
            'type' => 'png',
        ],
    ], $command);
});

it('will allow many chromium arguments', function () {
    $command = Browsershot::url('https://example.com')
        ->addChromiumArguments([
            'my-custom-arg',
            'another-argument' => 'some-value',
            'yet-another-arg' => 'foo',
        ])
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [
                '--my-custom-arg',
                '--another-argument=some-value',
                '--yet-another-arg=foo',
            ],
            'type' => 'png',
        ],
    ], $command);
});

it('will set user data dir arg flag', function () {
    $dataDir = __DIR__.'/temp/session';
    $command = Browsershot::url('https://example.com')
        ->userDataDir($dataDir)
        ->createScreenshotCommand('screenshot.png');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [
                "--user-data-dir={$dataDir}",
            ],
            'type' => 'png',
        ],
    ], $command);
});

it('will apply manipulations when taking screen shots', function () {
    $screenShot = Browsershot::url('https://example.com')
        ->windowSize(1920, 1080)
        ->fit(Manipulations::FIT_FILL, 200, 200)
        ->screenshot();

    $targetPath = __DIR__.'/temp/testScreenshot.png';

    file_put_contents($targetPath, $screenShot);

    expect($targetPath)->toBeFile();
    expect(getimagesize($targetPath)[0])->toEqual(200);
    expect(getimagesize($targetPath)[1])->toEqual(200);
});

it('will connect to remote instance and take screenshot', function () {
    $instance = Browsershot::url('https://example.com')
        ->setRemoteInstance();

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
            'remoteInstanceUrl' => 'http://127.0.0.1:9222',
        ],
    ], $instance->createScreenshotCommand('screenshot.png'));

    /*
     * to test the connection, uncomment the following code, and make sure you are running a chrome/chromium instance locally,
     * with the following param: --headless --remote-debugging-port=9222
     */
    /*
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    file_put_contents($targetPath, $instance->screenshot());
    expect($targetPath)->toBeFile();
    */
});

it('will connect to a custom remote instance and take screenshot', function () {
    $instance = Browsershot::url('https://example.com')
        ->setRemoteInstance('127.0.0.1', 9999);

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
            'remoteInstanceUrl' => 'http://127.0.0.1:9999',
        ],
    ], $instance->createScreenshotCommand('screenshot.png'));

    /*
    * to test the connection, uncomment the following code, and make sure you are running a chrome/chromium instance locally,
    * with the following params: --headless --remote-debugging-port=9999
    */
    /*
    $targetPath = __DIR__.'/temp/testScreenshot.png';

    file_put_contents($targetPath, $instance->screenshot());
    expect($targetPath)->toBeFile();
    */
});

it('will connect to a custom ws endpoint and take screenshot', function () {
    $instance = Browsershot::url('https://example.com')
        ->setWSEndpoint('wss://chrome.browserless.io/');

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
            'browserWSEndpoint' => 'wss://chrome.browserless.io/',
        ],
    ], $instance->createScreenshotCommand('screenshot.png'));

    // It should be online so mis-use the assetsContains because a 4xx error won't contain the word "browerless".
    $html = Browsershot::url('https://chrome.browserless.io/json/')
        ->bodyHtml();

    // If it's offline then this will fail.
    expect($html)->toContain('chrome.browserless.io');

    /* Now that we now the domain is online, assert the screenshot.
     * Although we can't be sure, because Browsershot itself falls back to launching a chromium instance in browser.js
    */

    $targetPath = __DIR__.'/temp/testScreenshot.png';

    file_put_contents($targetPath, $instance->screenshot());
    expect($targetPath)->toBeFile();
});

it('will allow passing environment variables', function () {
    $instance = Browsershot::url('https://example.com')
        ->setEnvironmentOptions([
            'TZ' => 'Pacific/Auckland',
        ]);

    $this->assertEquals([
        'url' => 'https://example.com',
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
            'env' => [
                'TZ' => 'Pacific/Auckland',
            ],
        ],
    ], $instance->createScreenshotCommand('screenshot.png'));
});

it('can take a scaled screenshot', function () {
    $targetPath = __DIR__.'/temp/testScreenshot.pdf';

    Browsershot::url('https://example.com')
        ->scale(0.5)
        ->save($targetPath);

    expect($targetPath)->toBeFile();
});

it('can throw an error when response is unsuccessful', function () {
    $url = 'https://google.com/404';

    $this->expectException(UnsuccessfulResponse::class);
    $this->expectExceptionMessage("The given url `{$url}` responds with code 404");

    $targetPath = __DIR__.'/temp/notExists.pdf';

    Browsershot::url($url)
        ->preventUnsuccessfulResponse()
        ->save($targetPath);

    $this->assertFileDoesNotExist($targetPath);
})->skip();

it('will allow passing a content url', function () {
    $instance = Browsershot::html('<h1>Hello world!!</h1>')
        ->setContentUrl('https://example.com');

    $response = $instance->createScreenshotCommand('screenshot.png');

    $responseUrl = $response['url'];
    unset($response['url']);

    $this->assertEquals([
        'action' => 'screenshot',
        'options' => [
            'path' => 'screenshot.png',
            'viewport' => [
                'width' => 800,
                'height' => 600,
            ],
            'args' => [],
            'type' => 'png',
            'displayHeaderFooter' => false,
            'contentUrl' => 'https://example.com',
        ],
    ], $response);

    $this->assertStringContainsString("file://", $responseUrl);
});

it('can get the console messages', function () {
    $consoleMessages = Browsershot::url('https://bitsofco.de/styling-broken-images/')->consoleMessages();

    expect($consoleMessages)->toBeArray()
        ->and($consoleMessages[0]['type'])->toBeString()
        ->and($consoleMessages[0]['message'])->toBeString()
        ->and($consoleMessages[0]['location']['url'])->toBeString();
});

it('can get the failed requests', function () {
    $failedRequests = Browsershot::url('https://bitsofco.de/styling-broken-images/')->failedRequests();

    expect($failedRequests)->toBeArray()
        ->and($failedRequests[0]['status'])->toBe(404)
        ->and($failedRequests[0]['url'])->toBe('https://bitsofco.de/broken.jpg/');
});
