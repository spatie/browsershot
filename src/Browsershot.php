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
    protected $nodeBinary = 'node';
    protected $npmBinary = 'npm';
    protected $includePath = '$PATH:/usr/local/bin';
    protected $networkIdleTimeout = 0;
    protected $clip = null;
    protected $deviceScaleFactor = 1;
    protected $format = null;
    protected $fullPage = false;
    protected $html = '';
    protected $landscape = false;
    protected $margins = null;
    protected $pages = '';
    protected $paperHeight = 0;
    protected $paperWidth = 0;
    protected $showBackground = false;
    protected $showBrowserHeaderAndFooter = false;
    protected $temporaryHtmlDirectory;
    protected $timeout = 60;
    protected $url = '';
    protected $userAgent = '';
    protected $windowHeight = 600;
    protected $windowWidth = 800;

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

    public function setNodeBinary(string $nodeBinary)
    {
        $this->nodeBinary = $nodeBinary;

        return $this;
    }

    public function setNpmBinary(string $npmBinary)
    {
        $this->npmBinary = $npmBinary;

        return $this;
    }

    public function setIncludePath(string $includePath)
    {
        $this->includePath = $includePath;

        return $this;
    }

    public function setNetworkIdleTimeout(int $networkIdleTimeout)
    {
        $this->networkIdleTimeout = $networkIdleTimeout;

        return $this;
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

        $this->hideBrowserHeaderAndFooter();

        return $this;
    }

    public function clip(int $x, int $y, int $width, int $height)
    {
        $this->clip = compact('x', 'y', 'width', 'height');

        return $this;
    }

    public function showBrowserHeaderAndFooter()
    {
        $this->showBrowserHeaderAndFooter = true;

        return $this;
    }

    public function hideBrowserHeaderAndFooter()
    {
        $this->showBrowserHeaderAndFooter = false;

        return $this;
    }

    public function deviceScaleFactor(int $deviceScaleFactor)
    {
        // Google Chrome currently supports values of 1, 2, and 3.
        $this->deviceScaleFactor = max(1, min(3, $deviceScaleFactor));

        return $this;
    }

    public function fullPage()
    {
        $this->fullPage = true;

        return $this;
    }

    public function showBackground()
    {
        $this->showBackground = true;

        return $this;
    }

    public function hideBackground()
    {
        $this->showBackground = false;

        return $this;
    }

    public function landscape(bool $landscape = true)
    {
        $this->landscape = $landscape;

        return $this;
    }

    public function margins(int $top, int $right, int $bottom, int $left)
    {
        $this->margins = compact('top', 'right', 'bottom', 'left');

        return $this;
    }

    public function pages(string $pages)
    {
        $this->pages = $pages;

        return $this;
    }

    public function paperSize(int $width, int $height)
    {
        $this->paperWidth = $width;
        $this->paperHeight = $height;

        return $this;
    }

    // paper format
    public function format(string $format)
    {
        $this->format = $format;

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
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath);
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
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath);
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

        $command = $this->createCommand($url, 'screenshot', ['path' => $targetPath]);

        if ($this->fullPage) {
            $command['options']['fullPage'] = true;
        }

        if ($this->clip) {
            $command['options']['clip'] = $this->clip;
        }

        return $command;
    }

    public function createPdfCommand($targetPath): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $command = $this->createCommand($url, 'pdf', ['path' => $targetPath]);

        if ($this->showBrowserHeaderAndFooter) {
            $command['options']['displayHeaderFooter'] = true;
        }

        if ($this->showBackground) {
            $command['options']['printBackground'] = true;
        }

        if ($this->landscape) {
            $command['options']['landscape'] = true;
        }

        if ($this->margins) {
            $command['options']['margin'] = [
                'top' => $this->margins['top'].'mm',
                'right' => $this->margins['right'].'mm',
                'bottom' => $this->margins['bottom'].'mm',
                'left' => $this->margins['left'].'mm',
            ];
        }

        if ($this->pages) {
            $command['options']['pageRanges'] = $this->pages;
        }

        if ($this->paperWidth > 0 && $this->paperHeight > 0) {
            $command['options']['width'] = $this->paperWidth.'mm';
            $command['options']['height'] = $this->paperHeight.'mm';
        }

        if ($this->format) {
            $command['options']['format'] = $this->format;
        }

        return $command;
    }

    protected function createCommand(string $url, string $action, array $options = []): array
    {
        $command = compact('url', 'action', 'options');

        $command['options']['viewport'] = [
            'width' => $this->windowWidth,
            'height' => $this->windowHeight,
        ];

        if ($this->userAgent) {
            $command['options']['userAgent'] = $this->userAgent;
        }

        if ($this->deviceScaleFactor > 1) {
            $command['options']['viewport']['deviceScaleFactor'] = $this->deviceScaleFactor;
        }

        if ($this->networkIdleTimeout > 0) {
            $command['options']['networkIdleTimeout'] = $this->networkIdleTimeout;
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
        $setIncludePathCommand = "PATH={$this->includePath}";

        $setNodePathCommand = "NODE_PATH=`{$this->nodeBinary} {$this->npmBinary} root -g`";

        $binPath = __DIR__.'/../bin/browser.js';

        $fullCommand =
            $setIncludePathCommand.' '
            .$setNodePathCommand.' '
            .$this->nodeBinary.' '
            .escapeshellarg($binPath).' '
            .escapeshellarg(json_encode($command));

        $process = (new Process($fullCommand))->setTimeout($this->timeout);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
