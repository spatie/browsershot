<?php

namespace Spatie\Browsershot;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;

/** @mixin \Spatie\Image\Manipulations */
class Browsershot
{
    protected $url = '';
    protected $html = '';

    protected $pathToChrome = '';
    protected $timeout = 60;

    protected $windowWidth = 0;
    protected $windowHeight = 0;
    protected $disableGpu = true;
    protected $hideScrollbars = true;
    protected $userAgent = '';
    protected $deviceScaleFactor = 1;

    protected $temporaryHtmlDirectory;

    /** @var \Spatie\Image\Manipulations */
    protected $imageManipulations;

    public static function url(string $url)
    {
        return (new static)->setUrl($url);
    }

    public static function html(string $html)
    {
        return (new static)->setHtml($html);
    }

    public function __construct(string $url = '')
    {
        $this->url = $url;

        $this->imageManipulations = new Manipulations();
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
        $this->html = '';

        return $this;
    }

    public function setHtml(string $html)
    {
        $this->html = $html;
        $this->url = '';

        return $this;
    }

    public function setChromePath(string $pathToChrome)
    {
        $this->pathToChrome = $pathToChrome;

        return $this;
    }

    public function enableGpu()
    {
        $this->disableGpu = false;

        return $this;
    }

    public function disableGpu()
    {
        $this->disableGpu = true;

        return $this;
    }

    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function userAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function showScrollbars()
    {
        $this->hideScrollbars = false;

        return $this;
    }

    public function hideScrollbars()
    {
        $this->hideScrollbars = true;

        return $this;
    }

    public function windowSize(int $width, int $height)
    {
        $this->windowWidth = $width;
        $this->windowHeight = $height;

        return $this;
    }

    public function deviceScaleFactor(int $deviceScaleFactor)
    {
        // Google Chrome currently supports values of 1, 2, and 3.
        $this->deviceScaleFactor = max(1, min(3, $deviceScaleFactor));

        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->imageManipulations->$name(...$arguments);

        return $this;
    }

    public function save(string $targetPath)
    {
        if (strtolower(pathinfo($targetPath, PATHINFO_EXTENSION)) === 'pdf') {
            return $this->savePdf($targetPath);
        }

        $temporaryDirectory = (new TemporaryDirectory())->create();

        try {
            $command = $this->createScreenshotCommand($temporaryDirectory->path());

            $process = (new Process($command))->setTimeout($this->timeout);

            $process->run();

            $this->cleanupTemporaryHtmlFile();

            if (! $process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $screenShotPath = $temporaryDirectory->path('screenshot.png');

            if (! file_exists($screenShotPath)) {
                throw CouldNotTakeBrowsershot::chromeOutputEmpty($screenShotPath, $process);
            }

            rename($screenShotPath, $targetPath);
        } finally {
            $temporaryDirectory->delete();
        }

        if (! $this->imageManipulations->isEmpty()) {
            $this->applyManipulations($targetPath);
        }
    }

    public function savePdf(string $targetPath)
    {
        $command = $this->createPdfCommand($targetPath);

        $process = (new Process($command))->setTimeout($this->timeout);

        $process->run();

        $this->cleanupTemporaryHtmlFile();
    }

    public function applyManipulations(string $imagePath)
    {
        Image::load($imagePath)
            ->manipulate($this->imageManipulations)
            ->save();
    }

    public function createScreenshotCommand(string $workingDirectory): string
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $command = 'cd '
            .escapeshellarg($workingDirectory)
            .';'
            .escapeshellarg($this->findChrome())
            .' --headless --screenshot '
            .escapeshellarg($url);

        if ($this->disableGpu) {
            $command .= ' --disable-gpu';
        }

        if ($this->windowWidth > 0) {
            $command .= ' --window-size='
                .escapeshellarg($this->windowWidth)
                .','
                .escapeshellarg($this->windowHeight);
        }

        if ($this->hideScrollbars) {
            $command .= ' --hide-scrollbars';
        }

        if (! empty($this->userAgent)) {
            $command .= ' --user-agent='.escapeshellarg($this->userAgent);
        }

        if ($this->deviceScaleFactor > 1) {
            $command .= ' --force-device-scale-factor='.escapeshellarg($this->deviceScaleFactor);
        }

        return $command;
    }

    protected function createPdfCommand($targetPath): string
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $command =
              escapeshellarg($this->findChrome())
            ." --headless --print-to-pdf={$targetPath}";

        if ($this->disableGpu) {
            $command .= ' --disable-gpu';
        }

        if ($this->hideScrollbars) {
            $command .= ' --hide-scrollbars';
        }

        if (! empty($this->userAgent)) {
            $command .= ' --user-agent='.escapeshellarg($this->userAgent);
        }

        $command .= ' '.escapeshellarg($url);

        return $command;
    }

    protected function createTemporaryHtmlFile(): string
    {
        $this->temporaryHtmlDirectory = (new TemporaryDirectory())->create();

        file_put_contents($temporaryHtmlFile = $this->temporaryHtmlDirectory->path('index.html'), $this->html);

        return "file://{$temporaryHtmlFile}";
    }

    protected function cleanupTemporaryHtmlFile()
    {
        if ($this->temporaryHtmlDirectory) {
            $this->temporaryHtmlDirectory->delete();
        }
    }

    protected function findChrome(): string
    {
        if (! empty($this->pathToChrome)) {
            return $this->pathToChrome;
        }

        return ChromeFinder::forCurrentOperatingSystem();
    }
}
