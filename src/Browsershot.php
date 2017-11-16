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
    protected $nodeBinary = null;
    protected $npmBinary = null;
    protected $includePath = '$PATH:/usr/local/bin';
    protected $networkIdleTimeout = 0;
    protected $clip = null;
    protected $deviceScaleFactor = 1;
    protected $format = null;
    protected $fullPage = false;
    protected $html = '';
    protected $ignoreHttpsErrors = false;
    protected $landscape = false;
    protected $margins = null;
    protected $noSandbox = false;
    protected $pages = '';
    protected $paperHeight = 0;
    protected $paperWidth = 0;
    protected $proxyServer = '';
    protected $showBackground = false;
    protected $showScreenshotBackground = true;
    protected $showBrowserHeaderAndFooter = false;
    protected $temporaryHtmlDirectory;
    protected $timeout = 60;
    protected $url = '';
    protected $userAgent = '';
    protected $windowHeight = 600;
    protected $windowWidth = 800;
    protected $mobile = false;
    protected $touch = false;
    protected $dismissDialogs = false;
    protected $additionalOptions = [];

    /** @var \Spatie\Image\Manipulations */
    protected $imageManipulations;

    /**
     * @param string $url
     *
     * @return static
     */
    public static function url(string $url)
    {
        return (new static)->setUrl($url);
    }

    /**
     * @param string $html
     *
     * @return static
     */
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

    /**
     * @deprecated This option is no longer supported by modern versions of Puppeteer.
     */
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

    public function setProxyServer(string $proxyServer)
    {
        $this->proxyServer = $proxyServer;

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
        $this->showScreenshotBackground = true;

        return $this;
    }

    public function hideBackground()
    {
        $this->showBackground = false;
        $this->showScreenshotBackground = false;

        return $this;
    }

    public function ignoreHttpsErrors()
    {
        $this->ignoreHttpsErrors = true;

        return $this;
    }

    public function mobile(bool $mobile = true)
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function touch(bool $touch = true)
    {
        $this->touch = $touch;

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

    public function noSandbox()
    {
        $this->noSandbox = true;

        return $this;
    }

    public function dismissDialogs()
    {
        $this->dismissDialogs = true;

        return $this;
    }

    public function pages(string $pages)
    {
        $this->pages = $pages;

        return $this;
    }

    public function paperSize(float $width, float $height)
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

    public function setOption($key, $value)
    {
        if (is_null($key)) {
            return $this;
        }

        $keys = array_reverse(explode('.', $key));

        $array = array_reduce($keys, function ($carry, $item) use ($value) {
            if (empty($carry)) {
                $carry = $value;
            }

            return [$item => $carry];
        }, []);

        $this->additionalOptions = array_merge_recursive($this->additionalOptions, $array);

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

        if (! $this->showScreenshotBackground) {
            $command['options']['omitBackground'] = true;
        }

        if ($this->dismissDialogs) {
            $command['options']['dismissDialogs'] = true;
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

    protected function getOptionArgs(): array
    {
        $args = [];

        if ($this->noSandbox) {
            $args[] = '--no-sandbox';
        }

        if ($this->proxyServer) {
            $args[] = '--proxy-server='.$this->proxyServer;
        }

        return $args;
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

        if ($this->touch) {
            $command['options']['viewport']['hasTouch'] = true;
        }

        if ($this->mobile) {
            $command['options']['viewport']['isMobile'] = true;
        }

        if ($this->networkIdleTimeout > 0) {
            $command['options']['networkIdleTimeout'] = $this->networkIdleTimeout;
        }

        if ($this->ignoreHttpsErrors) {
            $command['options']['ignoreHttpsErrors'] = $this->ignoreHttpsErrors;
        }

        $command['options']['args'] = $this->getOptionArgs();

        if (! empty($this->additionalOptions)) {
            $command['options'] = array_merge($command['options'], $this->additionalOptions);
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

        $nodeBinary = $this->nodeBinary ?: 'node';

        $setNodePathCommand = $this->getNodePathCommand($nodeBinary);

        $binPath = __DIR__.'/../bin/browser.js';

        $fullCommand =
            $setIncludePathCommand.' '
            .$setNodePathCommand.' '
            .$nodeBinary.' '
            .escapeshellarg($binPath).' '
            .escapeshellarg(json_encode($command));

        $process = (new Process($fullCommand))->setTimeout($this->timeout);

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    protected function getNodePathCommand(string $nodeBinary): string
    {
        if ($this->npmBinary) {
            return "NODE_PATH=`{$nodeBinary} {$this->npmBinary} root -g`";
        }

        return 'NODE_PATH=`npm root -g`';
    }

}
