<?php

namespace Spatie\Browsershot\Test;

use Spatie\Browsershot\Browsershot;

class BrowsershotTest extends TestCase
{
    public function setUp()
    {
        $this->emptyTempDirectory();
    }

    /** @test */
    public function it_can_get_the_body_html()
    {
        $html = $this
            ->getBrowsershotForCurrentEnvironment()
            ->bodyHtml();

        $this->assertContains('<h1>Example Domain</h1>', $html);
    }

    /** @test */
    public function it_can_take_a_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        $this
            ->getBrowsershotForCurrentEnvironment()
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_screenshot_of_arbitrary_html()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        $this->configureForCurrentEnvironment(Browsershot::html('<h1>Hello world!!</h1>'))
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_high_density_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        $this
            ->getBrowsershotForCurrentEnvironment()
            ->deviceScaleFactor(2)
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_save_a_pdf_by_using_the_pdf_extension()
    {
        $targetPath = __DIR__.'/temp/testPdf.pdf';

        $this
            ->getBrowsershotForCurrentEnvironment()
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertEquals('application/pdf', mime_content_type($targetPath));
    }

    /** @test */
    public function it_can_use_the_methods_of_the_image_package()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.jpg';

        $this
            ->getBrowsershotForCurrentEnvironment()
            ->format('jpg')
            ->save($targetPath);

        $this->assertFileExists($targetPath);

        $this->assertMimeType('image/jpeg', $targetPath);
    }

    /** @test */
    public function it_can_create_a_command_to_generate_a_screenshot()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot 'https://example.com' --disable-gpu --hide-scrollbars", $command);
    }

    /** @test */
    public function it_can_enable_the_usage_of_the_gpu()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->enableGpu()
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot 'https://example.com' --hide-scrollbars", $command);
    }

    /** @test */
    public function it_can_show_scrollbars()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->showScrollbars()
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot 'https://example.com' --disable-gpu", $command);
    }

    /** @test */
    public function it_can_use_given_user_agent()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->userAgent('my_special_snowflake')
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot 'https://example.com' --disable-gpu --hide-scrollbars --user-agent='my_special_snowflake'", $command);
    }

    protected function getBrowsershotForCurrentEnvironment($url = 'https://example.com'): Browsershot
    {
        return $this->configureForCurrentEnvironment(Browsershot::url($url));
    }

    protected function configureForCurrentEnvironment(Browsershot $browsershot): Browsershot
    {
        if (getenv('TRAVIS')) {
            $browsershot->setChromePath('google-chrome-stable');
        }

        return $browsershot;
    }
}
