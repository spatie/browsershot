<?php

namespace Spatie\Browsershot;

use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class ChromeFinder
{
    public $paths = [
        'Darwin' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome',
        ],
        'Linux' => [
            '/usr/bin/google-chrome',
        ],
    ];

    public static function forCurrentOs()
    {
        return (new static)->getChromePathForOs(PHP_OS);
    }

    public function getChromePathForOs(string $os)
    {
        if (! array_key_exists($os, $this->paths)) {
            throw CouldNotTakeBrowsershot::osNotSupported($os);
        }

        foreach ($this->paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        CouldNotTakeBrowsershot::chromeNotFound($this->paths[$os]);
    }
}
