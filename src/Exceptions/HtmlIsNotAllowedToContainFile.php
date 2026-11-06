<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class HtmlIsNotAllowedToContainFile extends Exception
{
    public static function make(): static
    {
        return new static('The specified HTML contains a disallowed URL or path.');
    }
}
