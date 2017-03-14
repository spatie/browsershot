<?php

namespace Spatie\Browsershot;

use Exception;
use Intervention\Image\ImageManager;

class Browsershot
{
    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var int */
    protected $quality;

    /** @var string */
    protected $backgroundColor;

    /** @var string */
    protected $url;

    /** @var string */
    protected $binPath;

    /** @var int */
    protected $timeout;

    public function __construct($binPath = '', $width = 640, $height = 480, $quality = 60, $timeout = 5000, $backgroundColor = null)
    {
        if ($binPath === '') {
            $binPath = $this->getBinPath();
        }

        $this->binPath = $binPath;
        $this->width = $width;
        $this->height = $height;
        $this->quality = $quality;
        $this->backgroundColor = $backgroundColor;
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param string $binPath
     *
     * @return $this
     */
    public function setBinPath($binPath)
    {
        $this->binPath = $binPath;

        return $this;
    }

    /**
     * Get PhantomJS binary Path.
     *
     * @return string
     */
    public function getBinPath()
    {
        $binFile = 'phantomjs';

        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            $binFile = 'phantomjs.exe';
        }

        return realpath(dirname(__FILE__).'/../../../bin/'.$binFile);
    }

    /**
     * @param int $width
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setWidth($width)
    {
        if (! is_numeric($width)) {
            throw new Exception('Width must be numeric');
        }

        $this->width = $width;

        return $this;
    }

    /**
     * @param int $height
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setHeight($height)
    {
        if (! is_numeric($height)) {
            throw new Exception('Height must be numeric');
        }

        $this->height = $height;

        return $this;
    }

    /**
     * Set the image quality.
     *
     * @param int $quality
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setQuality($quality)
    {
        if (! is_numeric($quality) || $quality < 1 || $quality > 100) {
            throw new Exception('Quality must be a numeric value between 1 - 100');
        }

        $this->quality = $quality;

        return $this;
    }

    /**
     * Set the background color.
     *
     * @param string $backgroundColor
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        if (! strlen($backgroundColor) > 0) {
            throw new Exception('No background color specified');
        }

        $this->backgroundColor = $backgroundColor;

        return $this;
    }

    /**
     * Set to height so the whole page will be rendered.
     *
     * @return $this
     */
    public function setHeightToRenderWholePage()
    {
        $this->height = 0;

        return $this;
    }

    /**
     * @param string $url
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setUrl($url)
    {
        if (! strlen($url) > 0) {
            throw new Exception('No url specified');
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @param int $timeout
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        if (! is_numeric($timeout)) {
            throw new Exception('Height must be numeric');
        }

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Convert the webpage to an image.
     *
     * @param string $targetFile The path of the file where the screenshot should be saved
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function save($targetFile)
    {
        if ($targetFile == '') {
            throw new Exception('targetfile not set');
        }

        if (! in_array(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)), ['jpeg', 'jpg', 'png', 'ppm', 'bmp', 'pdf', 'gif'])) {
            throw new Exception('targetfile extension not valid');
        }

        if ($this->url == '') {
            throw new Exception('url not set');
        }

        if (filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            throw new Exception('url is invalid');
        }

        if (! file_exists($this->binPath)) {
            throw new Exception('binary does not exist');
        }

        $this->takeScreenShot($targetFile);

        if (! file_exists($targetFile) || filesize($targetFile) < 1024) {
            throw new Exception('could not create screenshot');
        }

        if ($this->height > 0) {
            $imageManager = new ImageManager();
            $imageManager
                ->make($targetFile)
                ->crop($this->width, $this->height, 0, 0)
                ->save($targetFile, $this->quality);
        }

        return true;
    }

    /**
     * Take the screenshot.
     *
     * @param $targetFile
     */
    protected function takeScreenShot($targetFile)
    {
        $tempJsFileHandle = tmpfile();

        fwrite($tempJsFileHandle, $this->getPhantomJsScript($targetFile));
        $tempFileName = stream_get_meta_data($tempJsFileHandle)['uri'];
        $cmd = escapeshellcmd("{$this->binPath} --ssl-protocol=any --ignore-ssl-errors=true ".$tempFileName);

        shell_exec($cmd);

        fclose($tempJsFileHandle);
    }

    /**
     * Get the script to be executed by phantomjs.
     *
     * @param string $targetFile
     *
     * @return string
     */
    protected function getPhantomJsScript($targetFile)
    {
        return "
            var page = require('webpage').create();
            page.settings.javascriptEnabled = true;
            page.settings.resourceTimeout = ".$this->timeout.';
            page.viewportSize = { width: '.$this->width.', height: '.($this->height == 0 ? 1 : $this->height)." };
            page.open('{$this->url}', function() {
                if (".($this->backgroundColor ? 'true' : 'false').") {
                    page.evaluate(function() {
                        var style = document.createElement('style'),
                            text = document.createTextNode('body { background: {$this->backgroundColor} }');
                        style.setAttribute('type', 'text/css');
                        style.appendChild(text);
                        document.head.insertBefore(style, document.head.firstChild);
                    });
                }
                window.setTimeout(function(){
                    page.render('{$targetFile}');
                    phantom.exit();
                }, {$this->timeout});
            });
        ";
    }
}
