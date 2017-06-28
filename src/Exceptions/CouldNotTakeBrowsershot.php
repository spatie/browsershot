<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class CouldNotTakeBrowsershot extends Exception
{
    public static function osNotSupported(string $os)
    {
        return new static("The current os `{$os}` is not supported");
    }

    public static function chromeNotFound(array $locations)
    {
        $locations = implode(', ', $locations);

        return new static("Did not find Chrome at these locations: {$locations}");
    }
}
