<?php

namespace Spatie\Browsershot;

use Exception;
use \Intervention\Image\Image;

/**
 * Class Browsershot
 * @package Spatie\Browsershot
 */

class Browsershot {

    /**
     * @var int
     */
    private $width;
    /**
     * @var int
     */
    private $height;
    /**
     * @var
     */
    private $URL;
    /**
     * @var string
     */
    private $binPath;


    /**
     * @param string $binPath The path to the phantomjs binary
     */
    public function __construct($binPath = '')
    {

        if ($binPath == '') {
            $this->binPath = __DIR__ . '/bin/phantomjs';
        }

        $this->width = 640;
        $this->height = 480;

        return $this;
    }

    /**
     * @param string $binPath The path to the phantomjs binary
     * @return $this
     */
    public function setBinPath($binPath)
    {
        $this->binPath = $binPath;
        return $this;
    }

    /**
     * @param int $width The required with of the screenshot
     * @return $this
     * @throws \Exception
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
     * @param int $height The required height of the screenshot
     * @return $this
     * @throws \Exception
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
     * @param string $url The website of which a screenshot should be make
     * @return $this
     * @throws \Exception
     */
    public function setURL($url)
    {
        if (! strlen($url) > 0 ) {
            throw new Exception('No url specified');
        }

        $this->URL = $url;
        return $this;
    }

    /**
     *
     * Convert the webpage to an image
     *
     * @param string $targetFile The path of the file where the screenshot should be saved
     * @return bool
     * @throws \Exception
     */
    public function save($targetFile)
    {
        if ($targetFile == '') {
            throw new Exception('targetfile not set');
        }

        if (! file_exists($this->binPath)) {
            throw new Exception('binary does not exist');
        }

        if ($this->URL == '') {
            throw new Exception('url not set');
        }

        if (filter_var($this->URL, FILTER_VALIDATE_URL) === FALSE) {
            throw new Exception('url is invalid');
        }

        $tempJsFileHandle = tmpfile();

        $fileContent= "
            var page = require('webpage').create();
            page.settings.javascriptEnabled = true;
            page.viewportSize = { width: " . $this->width . ", height: " . $this->height . " };
            page.open('" . $this->URL . "', function() {
               window.setTimeout(function(){
                page.render('" . $targetFile . "');
                phantom.exit();
            }, 5000); // give phantomjs 5 seconds to process all javascript
        });";

        fwrite($tempJsFileHandle, $fileContent);
        $tempFileName = stream_get_meta_data($tempJsFileHandle)['uri'];
        $cmd = escapeshellcmd("{$this->binPath} " . $tempFileName);

        shell_exec($cmd);

        fclose($tempJsFileHandle);

        if (! file_exists($targetFile) OR filesize($targetFile) < 1024)
        {
            throw new Exception('could not create screenshot');
        }

        Image::make($targetFile)
            ->crop($this->width, $this->height, 0, 0)
            ->save($targetFile, 60);

        return true;
    }


}