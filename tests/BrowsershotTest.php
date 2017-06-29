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
    public function it_can_be_instanciated()
    {
        $browsershot = new Browsershot('spatie.be');

        $this->assertInstanceOf(Browsershot::class, $browsershot);
    }

    /** @test */
    public function it_can_take_a_screenshot_on_macos()
    {
        $this->skipIfNotRunningonMacOS();

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::url('https://spatie.be')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    /** @test */
    public function it_can_take_a_screenshot_on_travis()
    {
        $this->skipIfNotRunningonTravis();

        $targetPath = __DIR__.'/temp/testScreenshot.png';

        Browsershot::url('https://spatie.be')
            ->setChromePath('google-chrome-stable')
            ->save($targetPath);

        $this->assertFileExists($targetPath);
    }

    protected function emptyTempDirectory()
    {
        $tempDirPath = __DIR__.'/temp';

        $files = scandir($tempDirPath);

        foreach ($files as $file) {
            if (! in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }
}
