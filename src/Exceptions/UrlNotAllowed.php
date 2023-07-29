<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class UrlNotAllowed extends Exception
{
    public static function make()
    {
        return new static("URL scheme must be http:// or https://");
    }
}
