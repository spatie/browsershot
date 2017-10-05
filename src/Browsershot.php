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

    protected $timeout = 60;

    protected $windowWidth = 800;
    protected $windowHeight = 600;
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

        $command = $this->createScreenshotCommand($targetPath);

        $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (! file_exists($targetPath)) {
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath, $process);
        }

        if (! $this->imageManipulations->isEmpty()) {
            $this->applyManipulations($targetPath);
        }
    }

    public function bodyHtml(): string
    {
        $command = $this->createBodyHtmlCommand();

        return $this->callBrowser($command);
    }

    public function savePdf(string $targetPath)
    {
        $command = $this->createPdfCommand($targetPath);

        $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (! file_exists($targetPath)) {
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath, $process);
        }
    }

    public function applyManipulations(string $imagePath)
    {
        Image::load($imagePath)
            ->manipulate($this->imageManipulations)
            ->save();
    }

    public function createBodyHtmlCommand(): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $this->createCommand($url, 'content');
    }

    public function createScreenshotCommand(string $targetPath): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $this->createCommand($url, 'screenshot', [ 'path' => $targetPath ]);
    }

    protected function createPdfCommand($targetPath): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $this->createCommand($url, 'pdf', [ 'path' => $targetPath ]);
    }

    protected function createCommand(string $url, string $action, array $options = []): array
    {
        $command = compact('url', 'action', 'options');

        $command['options']['viewport'] = [
            'width' => $this->windowWidth,
            'height' => $this->windowHeight
        ];

        if ($this->userAgent) {
            $command['options']['userAgent'] = $this->userAgent;
        }

        if ($this->deviceScaleFactor > 1) {
            $command['options']['viewport']['deviceScaleFactor'] = $this->deviceScaleFactor;
        }

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

    protected function callBrowser(array $command)
    {
        $binPath = __DIR__ . '/../bin/browser.js';

        $cli = 'NODE_PATH=`npm root -g` '
            .escapeshellarg($binPath) . ' '
            .escapeshellarg(json_encode($command));

        $process = (new Process($cli))->setTimeout($this->timeout);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
