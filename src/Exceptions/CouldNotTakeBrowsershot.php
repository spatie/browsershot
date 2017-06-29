<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class CouldNotTakeBrowsershot extends Exception
{
    public static function osNotSupported(string $os)
    {
        return new static("The current os `{$os}` is not supported");
    }

    /**
     * @param array|string $locations
     *
     * @return static
     */
    public static function chromeNotFound($locations)
    {
        if (! is_array($locations)) {
            $locations = [$locations];
        }

        $locations = implode(', ', $locations);

        return new static("Did not find Chrome at: {$locations}");
    }
}
