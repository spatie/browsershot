<?php

namespace Spatie\Browsershot;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Symfony\Component\Process\Process;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Spatie\Browsershot\Exceptions\ElementNotFound;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Symfony\Component\Process\Exception\ProcessFailedException;

/** @mixin \Spatie\Image\Manipulations */
class Browsershot
{
    protected $nodeBinary = null;
    protected $npmBinary = null;
    protected $nodeModulePath = null;
    protected $includePath = '$PATH:/usr/local/bin';
    protected $binPath = null;
    protected $html = '';
    protected $noSandbox = false;
    protected $proxyServer = '';
    protected $showBackground = false;
    protected $showScreenshotBackground = true;
    protected $screenshotType = 'png';
    protected $screenshotQuality = null;
    protected $temporaryHtmlDirectory;
    protected $timeout = 60;
    protected $url = '';
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

        $this->windowSize(800, 600);
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

    public function setBinPath(string $binPath)
    {
        $this->binPath = $binPath;

        return $this;
    }

    public function setNodeModulePath(string $nodeModulePath)
    {
        $this->nodeModulePath = $nodeModulePath;

        return $this;
    }

    public function setChromePath(string $executablePath)
    {
        $this->setOption('executablePath', $executablePath);

        return $this;
    }

    public function setExtraHttpHeaders(array $extraHTTPHeaders)
    {
        $this->setOption('extraHTTPHeaders', $extraHTTPHeaders);

        return $this;
    }

    public function click(string $selector, string $button = 'left', int $clickCount = 1, int $delay = 0)
    {
        $clicks = ($this->additionalOptions['clicks'] ?? []);

        $clicks[] = compact('selector', 'button', 'clickCount', 'delay');

        $this->setOption('clicks', $clicks);

        return $this;
    }

    /**
     * @deprecated This option is no longer supported by modern versions of Puppeteer.
     */
    public function setNetworkIdleTimeout(int $networkIdleTimeout)
    {
        $this->setOption('networkIdleTimeout');

        return $this;
    }

    public function waitUntilNetworkIdle(bool $strict = true)
    {
        $this->setOption('waitUntil', $strict ? 'networkidle0' : 'networkidle2');

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
        return $this->setOption('clip', compact('x', 'y', 'width', 'height'));
    }

    public function select($selector)
    {
        return $this->setOption('selector', $selector);
    }

    public function showBrowserHeaderAndFooter()
    {
        return $this->setOption('displayHeaderFooter', true);
    }

    public function hideBrowserHeaderAndFooter()
    {
        return $this->setOption('displayHeaderFooter', false);
    }

    public function hideHeader()
    {
        return $this->headerHtml('<p></p>');
    }

    public function hideFooter()
    {
        return $this->footerHtml('<p></p>');
    }

    public function headerHtml(string $html)
    {
        return $this->setOption('headerTemplate', $html);
    }

    public function footerHtml(string $html)
    {
        return $this->setOption('footerTemplate', $html);
    }

    public function deviceScaleFactor(int $deviceScaleFactor)
    {
        // Google Chrome currently supports values of 1, 2, and 3.
        return $this->setOption('viewport.deviceScaleFactor', max(1, min(3, $deviceScaleFactor)));
    }

    public function fullPage()
    {
        return $this->setOption('fullPage', true);
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

    public function setScreenshotType(string $type, int $quality = null)
    {
        $this->screenshotType = $type;

        if (! is_null($quality)) {
            $this->screenshotQuality = $quality;
        }

        return $this;
    }

    public function ignoreHttpsErrors()
    {
        return $this->setOption('ignoreHttpsErrors', true);
    }

    public function mobile(bool $mobile = true)
    {
        return $this->setOption('viewport.isMobile', true);
    }

    public function touch(bool $touch = true)
    {
        return $this->setOption('viewport.hasTouch', true);
    }

    public function landscape(bool $landscape = true)
    {
        return $this->setOption('landscape', $landscape);
    }

    public function margins(int $top, int $right, int $bottom, int $left)
    {
        return $this->setOption('margin', [
            'top' => $top.'mm',
            'right' => $right.'mm',
            'bottom' => $bottom.'mm',
            'left' =>  $left.'mm',
        ]);
    }

    public function noSandbox()
    {
        $this->noSandbox = true;

        return $this;
    }

    public function dismissDialogs()
    {
        return $this->setOption('dismissDialogs', true);
    }

    public function pages(string $pages)
    {
        return $this->setOption('pageRanges', $pages);
    }

    public function paperSize(float $width, float $height)
    {
        return $this
            ->setOption('width', $width.'mm')
            ->setOption('height', $height.'mm');
    }

    // paper format
    public function format(string $format)
    {
        return $this->setOption('format', $format);
    }

    public function timeout(int $timeout)
    {
        $this->timeout = $timeout;
        $this->setOption('timeout', $timeout * 1000);

        return $this;
    }

    public function userAgent(string $userAgent)
    {
        $this->setOption('userAgent', $userAgent);

        return $this;
    }

    public function emulateMedia(?string $media)
    {
        $this->setOption('emulateMedia', $media);

        return $this;
    }

    public function windowSize(int $width, int $height)
    {
        return $this
            ->setOption('viewport.width', $width)
            ->setOption('viewport.height', $height);
    }

    public function setDelay(int $delayInMilliseconds)
    {
        return $this->setOption('delay', $delayInMilliseconds);
    }

    public function setOption($key, $value)
    {
        $this->arraySet($this->additionalOptions, $key, $value);

        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->imageManipulations->$name(...$arguments);

        return $this;
    }

    public function save(string $targetPath)
    {
        $extension = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));

        if ($extension === '') {
            throw CouldNotTakeBrowsershot::outputFileDidNotHaveAnExtension($targetPath);
        }

        if ($extension === 'pdf') {
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

    public function screenshot(): string
    {
        $command = $this->createScreenshotCommand();

        $encoded_image = $this->callBrowser($command);

        return base64_decode($encoded_image);
    }

    public function pdf(): string
    {
        $command = $this->createPdfCommand();

        $encoded_pdf = $this->callBrowser($command);

        return base64_decode($encoded_pdf);
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

    public function evaluate(string $pageFunction): string
    {
        $command = $this->createEvaluateCommand($pageFunction);

        return $this->callBrowser($command);
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

    public function createScreenshotCommand($targetPath = null): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [
            'type' => $this->screenshotType,
        ];
        if ($targetPath) {
            $options['path'] = $targetPath;
        }

        if ($this->screenshotQuality) {
            $options['quality'] = $this->screenshotQuality;
        }

        $command = $this->createCommand($url, 'screenshot', $options);

        if (! $this->showScreenshotBackground) {
            $command['options']['omitBackground'] = true;
        }

        return $command;
    }

    public function createPdfCommand($targetPath = null): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [];
        if ($targetPath) {
            $options['path'] = $targetPath;
        }

        $command = $this->createCommand($url, 'pdf', $options);

        if ($this->showBackground) {
            $command['options']['printBackground'] = true;
        }

        return $command;
    }

    public function createEvaluateCommand(string $pageFunction): array
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        $options = [
            'pageFunction' => $pageFunction,
        ];

        return $this->createCommand($url, 'evaluate', $options);
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

        $command['options']['args'] = $this->getOptionArgs();

        if (! empty($this->additionalOptions)) {
            $command['options'] = array_merge_recursive($command['options'], $this->additionalOptions);
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

        $binPath = $this->binPath ?: __DIR__.'/../bin/browser.js';

        $fullCommand =
            $setIncludePathCommand.' '
            .$setNodePathCommand.' '
            .$nodeBinary.' '
            .escapeshellarg($binPath).' '
            .escapeshellarg(json_encode($command));

        $process = (new Process($fullCommand))->setTimeout($this->timeout);

        $process->run();

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }

        if ($process->getExitCode() === 2) {
            throw new ElementNotFound($this->additionalOptions['selector']);
        }

        throw new ProcessFailedException($process);
    }

    protected function getNodePathCommand(string $nodeBinary): string
    {
        if ($this->nodeModulePath) {
            return "NODE_PATH='{$this->nodeModulePath}'";
        }
        if ($this->npmBinary) {
            return "NODE_PATH=`{$nodeBinary} {$this->npmBinary} root -g`";
        }

        return 'NODE_PATH=`npm root -g`';
    }

    protected function arraySet(array &$array, string $key, $value): array
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
