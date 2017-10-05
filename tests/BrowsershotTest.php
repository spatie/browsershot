<?php

namespace Spatie\Browsershot\Test;

use Spatie\Browsershot\Browsershot;

class BrowsershotTest extends TestCase
{
    public function setUp()
    {
        // $this->emptyTempDirectory();
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
    public function it_can_save_a_pdf_by_using_the_pdf_extension()
    {
        $targetPath = __DIR__.'/temp/testPdf.pdf';

        Browsershot::url('https://example.com')
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertEquals('application/pdf', mime_content_type($targetPath));
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
            ->createScreenshotCommand('screenshot.png');

        $this->assertEquals([
            'url' => 'https://example.com',
            'action' => 'screenshot',
            'options' => [
                'path' => 'screenshot.png',
                'viewport' => [
                    'width' => 800,
                    'height' => 600
                ]
            ]
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
                    'height' => 600
                ],
                'userAgent' => 'my_special_snowflake'
            ]
        ], $command);
    }
}
