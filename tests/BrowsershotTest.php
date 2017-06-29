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
            ->windowSize(50, 50)
            ->save('tests/temp/my_screenshot.png');
    }

    protected function emptyTempDirectory()
    {
        $tempDirPath = __DIR__ . '/temp';

        $files = scandir($tempDirPath);

        foreach($files as $file) {
            if (! in_array($file, ['.', '..', '.gitignore'])) {
                unlink("{$tempDirPath}/{$file}");
            }
        }
    }
}