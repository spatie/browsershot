<?php

namespace Spatie\Browsershot;

use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Browsershot
{
    protected $url = '';

    protected $pathToChrome = '';

    /** @var int */
    protected $windowWidth = 0;

    /** @var int */
    protected $windowHeight = 0;

    /** @var bool */
    protected $disableGpu = true;

    public static function url(string $url)
    {
        return new static($url);
    }

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function windowsize(int $width, int $height)
    {
        $this->windowWidth = $width;
        $this->windowHeight = $height;

        return $this;
    }

    public function save(string $targetPath)
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();

        $process = $this->buildScreenshotProcess($temporaryDirectory->path());

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $screenShotPath = $temporaryDirectory->path('screenshot.png');

        rename($screenShotPath, $targetPath);

        $temporaryDirectory->delete();
    }

    public function setChromePath(string $pathToChrome)
    {
        if (! file_exists($pathToChrome)) {
            CouldNotTakeBrowsershot::chromeNotFound($pathToChrome);
        }

        $this->pathToChrome = $pathToChrome;
    }

    protected function buildScreenshotProcess(string $workingDirectory): Process
    {
        $command = "cd '{$workingDirectory}';'{$this->findChrome()}' --headless --screenshot {$this->url}";

        if ($this->disableGpu) {
            $command .= ' --disable-gpu';
        }

        if ($this->windowWidth > 0) {
            $command .= " --window-size={$this->windowWidth},{$this->windowHeight}";
        }

        return new Process($command);
    }

    protected function findChrome(): string
    {
        if (! empty($this->pathToChrome)) {
            return $this->pathToChrome;
        }

        return ChromeFinder::forCurrentOs();
    }
}
