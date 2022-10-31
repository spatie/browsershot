<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class JSLinkIsNotInAllowList extends Exception
{
    public static function make(string $jsLink)
    {
        return new static("The URL '" . $jsLink . "' is not in the allowList.");
    }
}
