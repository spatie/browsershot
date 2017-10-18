<?php

namespace Spatie\Browsershot\Test;

use Spatie\Browsershot\Browsershot;
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
            ->windowSize(1280, 800)
            ->save($targetPath);

        $this->assertFileExists($targetPath);
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
    public function it_can_use_the_methods_of_the_image_package()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.jpg';

        Browsershot::url('https://example.com')
            ->format('jpg')
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertMimeType('image/jpeg', $targetPath);
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_screenshot()
    {
        $command = Browsershot::url('https://example.com')
            ->clip(100, 50, 600, 400)
            ->deviceScaleFactor(2)
            ->fullPage()
            ->windowSize(1920, 1080)
            ->createScreenshotCommand('screenshot.png');

        $this->assertEquals([
            'url' => 'https://example.com',
            'action' => 'screenshot',
            'options' => [
                'clip' => ['x' => 100, 'y' => 50, 'width' => 600, 'height' => 400],
                'path' => 'screenshot.png',
                'fullPage' => true,
                'viewport' => [
                    'deviceScaleFactor' => 2,
                    'width' => 1920,
                    'height' => 1080,
                ],
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
                'margins' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
                'pageRanges' => '1-3',
                'width' => '210mm',
                'height' => '148mm',
                'viewport' => [
                    'width' => 800,
                    'height' => 600,
                ],
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
                'margins' => ['top' => '10mm', 'right' => '20mm', 'bottom' => '30mm', 'left' => '40mm'],
                'pageRanges' => '1-3',
                'format' => 'a4',
                'viewport' => [
                    'width' => 800,
                    'height' => 600,
                ],
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
            ],
        ], $command);
    }

    /** @test */
    public function it_can_set_another_node_binary()
    {
        $this->expectException(ProcessFailedException::class);

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
            ->setNodeBinary('non-existant/bin/wich/causes/an/exception')
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
    public function it_can_take_a_request_idle_timeout()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::html('Foo')
            ->setNetworkIdleTimeout(100)
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }
}
