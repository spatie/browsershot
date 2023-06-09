<?php

namespace Spatie\Browsershot;

use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Spatie\Browsershot\Exceptions\ElementNotFound;
use Spatie\Browsershot\Exceptions\FileDoesNotExistException;
use Spatie\Browsershot\Exceptions\FileUrlNotAllowed;
use Spatie\Browsershot\Exceptions\HtmlIsNotAllowedToContainFile;
use Spatie\Browsershot\Exceptions\UnsuccessfulResponse;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/** @mixin \Spatie\Image\Manipulations */
class Browsershot
{
    protected $nodeBinary = null;
    protected $npmBinary = null;
    protected $nodeModulePath = null;
    protected $includePath = '$PATH:/usr/local/bin:/opt/homebrew/bin';
    protected $binPath = null;
    protected $html = '';
    protected $noSandbox = false;
    protected $proxyServer = '';
    protected $showBackground = false;
    protected $showScreenshotBackground = true;
    protected $scale = null;
    protected $screenshotType = 'png';
    protected $screenshotQuality = null;
    protected $temporaryHtmlDirectory;
    protected $timeout = 60;
    protected $transparentBackground = false;
    protected $url = '';
    protected $postParams = [];
    protected $additionalOptions = [];
    protected $temporaryOptionsDirectory;
    protected $tempPath = '';
    protected $writeOptionsToFile = false;
    protected $chromiumArguments = [];

    /** @var \Spatie\Image\Manipulations */
    protected $imageManipulations;

    public const POLLING_REQUEST_ANIMATION_FRAME = 'raf';
    public const POLLING_MUTATION = 'mutation';

    /**
     * @param string $url
     *
     * @return static
     */
    public static function url(string $url)
    {
        return (new static())->setUrl($url);
    }

    /**
     * @param string $html
     *
     * @return static
     */
    public static function html(string $html)
    {
        return (new static())->setHtml($html);
    }

    public static function htmlFromFilePath(string $filePath): self
    {
        return (new static())->setHtmlFromFilePath($filePath);
    }

    public function __construct(string $url = '', bool $deviceEmulate = false)
    {
        $this->url = $url;

        $this->imageManipulations = new Manipulations();

        if (! $deviceEmulate) {
            $this->windowSize(800, 600);
        }
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

    public function setCustomTempPath(string $tempPath)
    {
        $this->tempPath = $tempPath;

        return $this;
    }

    public function post(array $postParams = [])
    {
        $this->postParams = $postParams;

        return $this;
    }

    public function useCookies(array $cookies, string $domain = null)
    {
        if (! count($cookies)) {
            return $this;
        }

        if (is_null($domain)) {
            $domain = parse_url($this->url)['host'];
        }

        $cookies = array_map(function ($value, $name) use ($domain) {
            return compact('name', 'value', 'domain');
        }, $cookies, array_keys($cookies));

        if (isset($this->additionalOptions['cookies'])) {
            $cookies = array_merge($this->additionalOptions['cookies'], $cookies);
        }

        $this->setOption('cookies', $cookies);

        return $this;
    }

    public function setExtraHttpHeaders(array $extraHTTPHeaders)
    {
        $this->setOption('extraHTTPHeaders', $extraHTTPHeaders);

        return $this;
    }

    public function setExtraNavigationHttpHeaders(array $extraNavigationHTTPHeaders)
    {
        $this->setOption('extraNavigationHTTPHeaders', $extraNavigationHTTPHeaders);

        return $this;
    }

    public function authenticate(string $username, string $password)
    {
        $this->setOption('authentication', compact('username', 'password'));

        return $this;
    }

    public function click(string $selector, string $button = 'left', int $clickCount = 1, int $delay = 0)
    {
        $clicks = $this->additionalOptions['clicks'] ?? [];

        $clicks[] = compact('selector', 'button', 'clickCount', 'delay');

        $this->setOption('clicks', $clicks);

        return $this;
    }

    public function selectOption(string $selector, string $value = '')
    {
        $dropdownSelects = $this->additionalOptions['selects'] ?? [];

        $dropdownSelects[] = compact('selector', 'value');

        $this->setOption('selects', $dropdownSelects);

        return $this;
    }

    public function type(string $selector, string $text = '', int $delay = 0)
    {
        $types = $this->additionalOptions['types'] ?? [];

        $types[] = compact('selector', 'text', 'delay');

        $this->setOption('types', $types);

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

    public function waitForFunction(string $function, $polling = self::POLLING_REQUEST_ANIMATION_FRAME, int $timeout = 0)
    {
        $this->setOption('functionPolling', $polling);
        $this->setOption('functionTimeout', $timeout);

        return $this->setOption('function', $function);
    }

    public function setUrl(string $url)
    {
        if (Helpers::stringStartsWith(strtolower($url), 'file://')) {
            throw FileUrlNotAllowed::make();
        }

        $this->url = $url;
        $this->html = '';

        return $this;
    }

    public function setHtmlFromFilePath(string $filePath): self
    {
        if (false === file_exists($filePath)) {
            throw new FileDoesNotExistException($filePath);
        }

        $this->url = 'file://'.$filePath;
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
        if (Helpers::stringContains(strtolower($html), 'file://')) {
            throw HtmlIsNotAllowedToContainFile::make();
        }

        $this->html = $html;
        $this->url = '';

        $this->hideBrowserHeaderAndFooter();

        return $this;
    }

    public function clip(int $x, int $y, int $width, int $height)
    {
        return $this->setOption('clip', compact('x', 'y', 'width', 'height'));
    }

    public function preventUnsuccessfulResponse(bool $preventUnsuccessfulResponse = true)
    {
        return $this->setOption('preventUnsuccessfulResponse', $preventUnsuccessfulResponse);
    }

    public function select($selector, $index = 0)
    {
        $this->selectorIndex($index);

        return $this->setOption('selector', $selector);
    }

    public function selectorIndex(int $index)
    {
        return $this->setOption('selectorIndex', $index);
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

    public function transparentBackground()
    {
        $this->transparentBackground = true;

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
        return $this->setOption('viewport.isMobile', $mobile);
    }

    public function touch(bool $touch = true)
    {
        return $this->setOption('viewport.hasTouch', $touch);
    }

    public function landscape(bool $landscape = true)
    {
        return $this->setOption('landscape', $landscape);
    }

    public function margins(float $top, float $right, float $bottom, float $left, string $unit = 'mm')
    {
        return $this->setOption('margin', [
            'top' => $top.$unit,
            'right' => $right.$unit,
            'bottom' => $bottom.$unit,
            'left' => $left.$unit,
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

    public function disableJavascript()
    {
        return $this->setOption('disableJavascript', true);
    }

    public function disableImages()
    {
        return $this->setOption('disableImages', true);
    }

    public function blockUrls($array)
    {
        return $this->setOption('blockUrls', $array);
    }

    public function blockDomains($array)
    {
        return $this->setOption('blockDomains', $array);
    }

    public function pages(string $pages)
    {
        return $this->setOption('pageRanges', $pages);
    }

    public function paperSize(float $width, float $height, string $unit = 'mm')
    {
        return $this
            ->setOption('width', $width.$unit)
            ->setOption('height', $height.$unit);
    }

    // paper format
    public function format(string $format)
    {
        return $this->setOption('format', $format);
    }

    public function scale(float $scale)
    {
        $this->scale = $scale;

        return $this;
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

    public function device(string $device)
    {
        $this->setOption('device', $device);

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

    public function delay(int $delayInMilliseconds)
    {
        return $this->setDelay($delayInMilliseconds);
    }

    public function setUserDataDir(string $absolutePath)
    {
        return $this->addChromiumArguments(['user-data-dir' => $absolutePath]);
    }

    public function userDataDir(string $absolutePath)
    {
        return $this->setUserDataDir($absolutePath);
    }

    public function writeOptionsToFile()
    {
        $this->writeOptionsToFile = true;

        return $this;
    }

    public function setOption($key, $value)
    {
        $this->arraySet($this->additionalOptions, $key, $value);

        return $this;
    }

    public function addChromiumArguments(array $arguments)
    {
        foreach ($arguments as $argument => $value) {
            if (is_numeric($argument)) {
                $this->chromiumArguments[] = "--$value";
            } else {
                $this->chromiumArguments[] = "--$argument=$value";
            }
        }

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

        $output = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (! file_exists($targetPath)) {
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath, $output, $command);
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

    public function base64Screenshot(): string
    {
        $command = $this->createScreenshotCommand();

        return $this->callBrowser($command);
    }

    public function screenshot(): string
    {
        if ($this->imageManipulations->isEmpty()) {
            $command = $this->createScreenshotCommand();

            $encodedImage = $this->callBrowser($command);

            return base64_decode($encodedImage);
        }

        $temporaryDirectory = (new TemporaryDirectory($this->tempPath))->create();

        $this->save($temporaryDirectory->path('screenshot.png'));

        $screenshot = file_get_contents($temporaryDirectory->path('screenshot.png'));

        $temporaryDirectory->delete();

        return $screenshot;
    }

    public function pdf(): string
    {
        $command = $this->createPdfCommand();

        $encoded_pdf = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        return base64_decode($encoded_pdf);
    }

    public function savePdf(string $targetPath)
    {
        $command = $this->createPdfCommand($targetPath);

        $output = $this->callBrowser($command);

        $this->cleanupTemporaryHtmlFile();

        if (! file_exists($targetPath)) {
            throw CouldNotTakeBrowsershot::chromeOutputEmpty($targetPath, $output);
        }
    }

    public function base64pdf(): string
    {
        $command = $this->createPdfCommand();

        return $this->callBrowser($command);
    }

    public function evaluate(string $pageFunction): string
    {
        $command = $this->createEvaluateCommand($pageFunction);

        return $this->callBrowser($command);
    }

    public function triggeredRequests(): array
    {
        $command = $this->createTriggeredRequestsListCommand();

        return json_decode($this->callBrowser($command), true);
    }

    /**
     * @return array{type: string, message: string, location:array}
     */
    public function consoleMessages(): array
    {
        $command = $this->createConsoleMessagesCommand();

        return json_decode($this->callBrowser($command), true);
    }

    public function failedRequests(): array
    {
        $command = $this->createFailedRequestsCommand();

        return json_decode($this->callBrowser($command), true);
    }

    public function applyManipulations(string $imagePath)
    {
        Image::load($imagePath)
            ->manipulate($this->imageManipulations)
            ->save();
    }

    public function createBodyHtmlCommand(): array
    {
        $url = $this->getFinalContentsUrl();

        return $this->createCommand($url, 'content');
    }

    public function createScreenshotCommand($targetPath = null): array
    {
        $url = $this->getFinalContentsUrl();

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
        $url = $this->getFinalContentsUrl();

        $options = [];

        if ($targetPath) {
            $options['path'] = $targetPath;
        }

        $command = $this->createCommand($url, 'pdf', $options);

        if ($this->showBackground) {
            $command['options']['printBackground'] = true;
        }

        if ($this->transparentBackground) {
            $command['options']['omitBackground'] = true;
        }

        if ($this->scale) {
            $command['options']['scale'] = $this->scale;
        }

        return $command;
    }

    public function createEvaluateCommand(string $pageFunction): array
    {
        $url = $this->getFinalContentsUrl();

        $options = [
            'pageFunction' => $pageFunction,
        ];

        return $this->createCommand($url, 'evaluate', $options);
    }

    public function createTriggeredRequestsListCommand(): array
    {
        $url = $this->html
            ? $this->createTemporaryHtmlFile()
            : $this->url;

        return $this->createCommand($url, 'requestsList');
    }

    public function createConsoleMessagesCommand(): array
    {
        $url = $this->html
            ? $this->createTemporaryHtmlFile()
            : $this->url;

        return $this->createCommand($url, 'consoleMessages');
    }

    public function createFailedRequestsCommand(): array
    {
        $url = $this->html
            ? $this->createTemporaryHtmlFile()
            : $this->url;

        return $this->createCommand($url, 'failedRequests');
    }

    public function setRemoteInstance(string $ip = '127.0.0.1', int $port = 9222): self
    {
        // assuring that ip and port does actually contains a value
        if ($ip && $port) {
            $this->setOption('remoteInstanceUrl', 'http://'.$ip.':'.$port);
        }

        return $this;
    }

    public function setWSEndpoint(string $endpoint): self
    {
        if (! is_null($endpoint)) {
            $this->setOption('browserWSEndpoint', $endpoint);
        }

        return $this;
    }

    public function usePipe(): self
    {
        $this->setOption('pipe', true);

        return $this;
    }

    public function setEnvironmentOptions(array $options = []): self
    {
        return $this->setOption('env', $options);
    }

    public function setContentUrl(string $contentUrl): self
    {
        return $this->html ? $this->setOption('contentUrl', $contentUrl) : $this;
    }

    protected function getOptionArgs(): array
    {
        $args = $this->chromiumArguments;

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

        if (! empty($this->postParams)) {
            $command['postParams'] = $this->postParams;
        }

        if (! empty($this->additionalOptions)) {
            $command['options'] = array_merge_recursive($command['options'], $this->additionalOptions);
        }

        return $command;
    }

    protected function createTemporaryHtmlFile(): string
    {
        $this->temporaryHtmlDirectory = (new TemporaryDirectory($this->tempPath))->create();

        file_put_contents($temporaryHtmlFile = $this->temporaryHtmlDirectory->path('index.html'), $this->html);

        return "file://{$temporaryHtmlFile}";
    }

    protected function cleanupTemporaryHtmlFile()
    {
        if ($this->temporaryHtmlDirectory) {
            $this->temporaryHtmlDirectory->delete();
        }
    }

    protected function createTemporaryOptionsFile(string $command): string
    {
        $this->temporaryOptionsDirectory = (new TemporaryDirectory($this->tempPath))->create();

        file_put_contents($temporaryOptionsFile = $this->temporaryOptionsDirectory->path('command.js'), $command);

        return "file://{$temporaryOptionsFile}";
    }

    protected function cleanupTemporaryOptionsFile()
    {
        if ($this->temporaryOptionsDirectory) {
            $this->temporaryOptionsDirectory->delete();
        }
    }

    protected function callBrowser(array $command): string
    {
        $fullCommand = $this->getFullCommand($command);

        $process = Process::fromShellCommandline($fullCommand)->setTimeout($this->timeout);

        $process->run();

        if ($process->isSuccessful()) {
            return rtrim($process->getOutput());
        }

        $this->cleanupTemporaryOptionsFile();
        $process->clearOutput();
        $exitCode = $process->getExitCode();

        if ($exitCode === 3) {
            throw new UnsuccessfulResponse($this->url, $process->getErrorOutput());
        }

        if ($exitCode === 2) {
            throw new ElementNotFound($this->additionalOptions['selector']);
        }

        throw new ProcessFailedException($process);
    }

    protected function getFullCommand(array $command)
    {
        $nodeBinary = $this->nodeBinary ?: 'node';

        $binPath = $this->binPath ?: __DIR__.'/../bin/browser.cjs';

        $optionsCommand = $this->getOptionsCommand(json_encode($command));

        if ($this->isWindows()) {
            $fullCommand =
                $nodeBinary.' '
                .escapeshellarg($binPath).' '
                .'"'
                .$optionsCommand
                .'"';

            return escapeshellcmd($fullCommand);
        }

        $setIncludePathCommand = "PATH={$this->includePath}";

        $setNodePathCommand = $this->getNodePathCommand($nodeBinary);

        return
            $setIncludePathCommand.' '
            .$setNodePathCommand.' '
            .$nodeBinary.' '
            .escapeshellarg($binPath).' '
            .$optionsCommand;
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

    protected function getOptionsCommand(string $command): string
    {
        if ($this->writeOptionsToFile) {
            $temporaryOptionsFile = $this->createTemporaryOptionsFile($command);
            $command = "-f {$temporaryOptionsFile}";
        }

        if ($this->isWindows()) {
            return str_replace('"', '\"', $command);
        }

        return escapeshellarg($command);
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

    public function initialPageNumber(int $initialPage = 1)
    {
        return $this
            ->setOption('initialPageNumber', ($initialPage - 1))
            ->pages($initialPage.'-');
    }

    private function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private function getFinalContentsUrl(): string
    {
        $url = $this->html ? $this->createTemporaryHtmlFile() : $this->url;

        return $url;
    }

    public function newHeadless(): self
    {
        return $this->setOption('newHeadless', true);
    }
}
