<?php

namespace Spatie\Browsershot\Test;

use PHPUnit\Framework\TestCase;
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
        Browsershot::url('https://spatie.be')
            ->windowSize(1024, 768)
            ->save('temp/my_screenshot.png');
    }

    protected function emptyTempDirectory()
    {
        $files = scandir(__DIR__.'/temp');

        foreach ($files as $file) {
            if (! in_array($file, ['.', '..', '.gitignore'])) {
                unlink($file);
            }
        }
    }
}
