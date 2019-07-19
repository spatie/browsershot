<?php

namespace Spatie\Browsershot\Test;

use Spatie\Image\Manipulations;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\ElementNotFound;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BrowsershotTest extends TestCase
{
    public function setUp()
    {
        $this->emptyTempDirectory();
    }

    /** @test */
    public function it_can_get_the_body_html()
    {
        $html = Browsershot::url('https://example.com')
            ->bodyHtml();

        $this->assertContains('<h1>Example Domain</h1>', $html);
    }

    /** @test */
    public function it_can_take_a_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::url('https://example.com')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_screenshot_of_arbitrary_html()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('<h1>Hello world!!</h1>')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_high_density_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::url('https://example.com')
            ->deviceScaleFactor(2)
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_cannot_save_without_an_extension()
    {
        $this->expectException(CouldNotTakeBrowsershot::class);

        $targetPath = __DIR__.'/temp/testScreenshot';

        Browsershot::url('https://example.com')
            ->save($targetPath);
    }

    /** @test */
    public function it_can_take_a_full_page_screenshot()
    {
        $targetPath = __DIR__.'/temp/fullpageScreenshot.png';

        Browsershot::url('https://github.com/spatie/browsershot')
            ->fullPage()
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_highly_customized_screenshot()
    {
        $targetPath = __DIR__.'/temp/customScreenshot.png';

        Browsershot::url('https://example.com')
            ->clip(290, 80, 700, 290)
            ->deviceScaleFactor(2)
            ->dismissDialogs()
            ->mobile()
            ->touch()
            ->windowSize(1280, 800)
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_screenshot_of_an_element_matching_a_selector()
    {
        $targetPath = __DIR__.'/temp/nodeScreenshot.png';

        Browsershot::url('https://example.com')
            ->select('div')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_throws_an_exception_if_the_selector_does_not_match_any_elements()
    {
        $this->expectException(ElementNotFound::class);
        $targetPath = __DIR__.'/temp/nodeScreenshot.png';

        Browsershot::url('https://example.com')
            ->select('not-a-valid-selector')
            ->save($targetPath);
    }

    /** @test */
    public function it_can_save_a_pdf_by_using_the_pdf_extension()
    {
        $targetPath = __DIR__.'/temp/testPdf.pdf';

        Browsershot::url('https://example.com')
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertEquals('application/pdf', mime_content_type($targetPath));
    }

    /** @test */
    public function it_can_save_a_highly_customized_pdf()
    {
        $targetPath = __DIR__.'/temp/customPdf.pdf';

        Browsershot::url('https://example.com')
            ->hideBrowserHeaderAndFooter()
            ->showBackground()
            ->landscape()
            ->margins(5, 25, 5, 25)
            ->pages('1')
            ->savePdf($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertEquals('application/pdf', mime_content_type($targetPath));
    }

    /** @test */
    public function it_can_handle_a_permissions_error()
    {
        $targetPath = '/cantWriteThisPdf.png';

        $this->expectException(ProcessFailedException::class);

        Browsershot::url('https://example.com')
            ->save($targetPath);
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_screenshot()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_screenshot_and_omit_the_background()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf()
    {
        $command = Browsershot::url('https://example.com')
            ->showBackground()
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf_with_a_custom_header()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf_with_a_custom_footer()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf_with_the_header_hidden()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf_with_the_footer_hidden()
    {
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
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_pdf_with_paper_format()
    {
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
    }

    /** @test */
    public function it_can_use_given_user_agent()
    {
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
    }

    /** @test */
    public function it_can_set_emulate_media_option()
    {
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
    }

    /** @test */
    public function it_can_set_emulate_media_option_to_null()
    {
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
    }

    /** @test */
    public function it_can_set_another_node_binary()
    {
        $this->expectException(ProcessFailedException::class);

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
            ->setBinPath(__DIR__.'/../browser.js')
            ->save($targetPath);
    }

    /** @test */
    public function it_can_set_another_chrome_executable_path()
    {
        $this->expectException(ProcessFailedException::class);

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
            ->setChromePath('non-existant/bin/wich/causes/an/exception')
            ->save($targetPath);
    }

    /** @test */
    public function it_can_set_another_bin_path()
    {
        $this->expectException(ProcessFailedException::class);

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
                ->setBinPath('non-existant/bin/wich/causes/an/exception')
                ->save($targetPath);
    }

    /** @test */
    public function it_can_set_the_include_path_and_still_works()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
            ->setIncludePath('$PATH:/usr/local/bin:/mypath')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_run_without_sandbox()
    {
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
    }

    /** @test */
    public function it_can_dismiss_dialogs()
    {
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
    }

    /** @test */
    public function it_can_disable_javascript()
    {
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
    }

    /** @test */
    public function it_can_ignore_https_errors()
    {
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
    }

    /** @test */
    public function it_can_use_a_proxy_server()
    {
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
    }

    /** @test */
    public function it_can_set_arbitrary_options()
    {
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
    }

    /** @test **/
    public function it_can_add_a_delay_before_taking_a_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        $delay = 2000;

        $start = round(microtime(true) * 1000);

        Browsershot::url('https://example.com')
            ->setDelay($delay)
            ->save($targetPath);

        $end = round(microtime(true) * 1000);

        $this->assertGreaterThanOrEqual($delay, $end - $start);
    }

    /** @test */
    public function it_can_get_the_output_of_a_screenshot()
    {
        $output = Browsershot::url('https://example.com')
            ->screenshot();

        $finfo = finfo_open();

        $mimeType = finfo_buffer($finfo, $output, FILEINFO_MIME_TYPE);

        $this->assertEquals($mimeType, 'image/png');
    }

    /** @test */
    public function it_can_get_the_output_of_a_pdf()
    {
        $output = Browsershot::url('https://example.com')
            ->pdf();

        $finfo = finfo_open();

        $mimeType = finfo_buffer($finfo, $output, FILEINFO_MIME_TYPE);

        $this->assertEquals($mimeType, 'application/pdf');
    }

    /** @test */
    public function it_can_save_to_temp_dir_with_background()
    {
        $targetPath = tempnam(sys_get_temp_dir(), 'bs_').'.jpg';
        Browsershot::url('https://example.com')
            ->background('white')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_wait_until_network_idle()
    {
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
    }

    /** @test */
    public function it_can_send_extra_http_headers()
    {
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
    }

    /**
     * @test
     */
    public function it_can_send_cookies()
    {
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
    }

    /** @test */
    public function it_can_click_on_the_page()
    {
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
    }

    /** @test */
    public function it_can_set_type_of_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.jpg';

        Browsershot::url('https://example.com')
            ->setScreenshotType('jpeg')
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertMimeType('image/jpeg', $targetPath);
    }

    /** @test */
    public function it_can_evaluate_page()
    {
        $result = Browsershot::url('https://example.com')
            ->evaluate('1 + 1');

        $this->assertEquals('2', $result);
    }

    /** @test **/
    public function it_can_add_a_timeout_to_puppeteer()
    {
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
    }

    /** @test **/
    public function it_can_add_a_wait_for_function_to_puppeteer()
    {
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
    }

    /** @test **/
    public function it_can_input_form_fields_and_post_and_get_the_body_html()
    {
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
    }

    /** @test **/
    public function it_can_input_form_fields_and_post_to_ssl_and_get_the_body_html()
    {
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
    }

    /** @test **/
    public function it_can_change_select_fields_and_post_and_get_the_body_html()
    {
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
    }

    /** @test */
    public function it_can_write_options_to_a_file_and_generate_a_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::url('https://example.com')
            ->writeOptionsToFile()
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_write_options_to_a_file_and_generate_a_pdf()
    {
        $targetPath = __DIR__.'/temp/testPdf.pdf';

        Browsershot::url('https://example.com')
            ->writeOptionsToFile()
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertEquals('application/pdf', mime_content_type($targetPath));
    }

    /** @test */
    public function it_can_generate_a_pdf_with_custom_paper_size_unit()
    {
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
    }

    /** @test */
    public function it_will_auto_prefix_chromium_arguments()
    {
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
    }

    /** @test */
    public function it_will_allow_many_chromium_arguments()
    {
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
    }

    /** @test */
    public function it_will_apply_manipulations_when_taking_screen_shots()
    {
        $screenShot = Browsershot::url('https://example.com')
            ->windowSize(1920, 1080)
            ->fit(Manipulations::FIT_FILL, 200, 200)
            ->screenshot();

        $targetPath = __DIR__.'/temp/testScreenshots.png';

        file_put_contents($targetPath, $screenShot);

        $this->assertFileExists($targetPath);
        $this->assertEquals(200, getimagesize($targetPath)[0]);
        $this->assertEquals(200, getimagesize($targetPath)[1]);
    }
}
