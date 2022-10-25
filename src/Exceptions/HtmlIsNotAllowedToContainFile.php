<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class HtmlIsNotAllowedToContainFile extends Exception
{
    public static function make()
    {
        return new static("The specified HTML contains `file://`. This is not allowed.");
    }
}
