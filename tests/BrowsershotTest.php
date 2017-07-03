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
    public function it_can_take_a_screenshot()
    {
        $targetPath = __DIR__.'/temp/testScreenshot.png';

        $this
            ->getBrowsershotForCurrentEnvironment()
            ->save($targetPath);

        $this->assertFileExists($targetPath);
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

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot https://example.com --disable-gpu --hide-scrollbars", $command);
    }

    /** @test */
    public function it_can_enable_the_usage_of_the_gpu()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->enableGpu()
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot https://example.com --hide-scrollbars", $command);
    }

    /** @test */
    public function it_can_show_scrollbars()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->showScrollbars()
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot https://example.com --disable-gpu", $command);
    }

    /** @test */
    public function it_can_use_given_user_agent()
    {
        $command = Browsershot::url('https://example.com')
            ->setChromePath('chrome')
            ->userAgent('my_special_snowflake')
            ->createScreenshotCommand('workingDir');

        $this->assertEquals("cd 'workingDir';'chrome' --headless --screenshot https://example.com --disable-gpu --hide-scrollbars --user-agent='my_special_snowflake'", $command);
    }

    protected function getBrowsershotForCurrentEnvironment(): Browsershot
    {
        $browsershot = Browsershot::url('https://example.com');

        if (getenv('TRAVIS')) {
            $browsershot->setChromePath('google-chrome-stable');
        }

        return $browsershot;
    }
}
