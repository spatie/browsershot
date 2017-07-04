<?php

namespace Spatie\Browsershot;

use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

class ChromeFinder
{
    protected $paths = [
        'Darwin' => [
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
        ],
        'Linux' => [
            '/usr/bin/google-chrome',
        ],
    ];

    public static function forCurrentOperatingSystem()
    {
        return (new static)->getChromePathForOperatingSystem(PHP_OS);
    }

    public function getChromePathForOperatingSystem(string $operatingSystem)
    {
        if (! array_key_exists($operatingSystem, $this->paths)) {
            throw CouldNotTakeBrowsershot::operatingSystemNotSupported($operatingSystem);
        }

        foreach ($this->paths[$operatingSystem] as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw CouldNotTakeBrowsershot::chromeNotFound($this->paths[$operatingSystem]);
    }
}
