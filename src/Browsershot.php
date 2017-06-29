<?php

namespace Spatie\Browsershot;

use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


/** @mixin \Spatie\Image\Manipulations */
class Browsershot
{
    protected $url = '';

    protected $pathToChrome = '';

    protected $windowWidth = 0;
    protected $windowHeight = 0;

    protected $disableGpu = true;

    protected $hideScrollbars = true;

    /** @var \Spatie\Image\Manipulations */
    protected $imageManipulations;

    public static function url(string $url)
    {
        return new static($url);
    }

    public function __construct(string $url)
    {
        $this->url = $url;

        $this->imageManipulations = new Manipulations();
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

    public function hideScrollbars()
    {
        $this->hideScrollbars = true;

        return $this;
    }

    public function showScrollbars()
    {
        $this->hideScrollbars = false;

        return $this;
    }

    public function windowSize(int $width, int $height)
    {
        $this->windowWidth = $width;
        $this->windowHeight = $height;

        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->imageManipulations->$name(...$arguments);

        return $this;
    }

    public function save(string $targetPath)
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();

        $process = $this->createScreenshotProcess($temporaryDirectory->path());

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $screenShotPath = $temporaryDirectory->path('screenshot.png');

        if (! file_exists($screenShotPath)) {
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($screenShotPath, $process);
        }

        rename($screenShotPath, $targetPath);

        $temporaryDirectory->delete();

        if (! $this->imageManipulations->isEmpty()) {
            $this->applyManipulations($targetPath);
        }
    }

    public function setChromePath(string $pathToChrome)
    {
        if (! file_exists($pathToChrome)) {
            CouldNotTakeBrowsershot::chromeNotFound($pathToChrome);
        }

        $this->pathToChrome = $pathToChrome;
    }

    public function applyManipulations(string $imagePath)
    {
        Image::load($imagePath)
            ->manipulate($this->imageManipulations)
            ->save();
    }

    public function createScreenshotProcess(string $workingDirectory): Process
    {
        $command = "cd '{$workingDirectory}';'{$this->findChrome()}' --headless --screenshot {$this->url}";

        if ($this->disableGpu) {
            $command .= ' --disable-gpu';
        }

        if ($this->windowWidth > 0) {
            $command .= " --window-size={$this->windowWidth},{$this->windowHeight}";
        }

        if ($this->hideScrollbars) {
            $command .= ' --hide-scrollbars';
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