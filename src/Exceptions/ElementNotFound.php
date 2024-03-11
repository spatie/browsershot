<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class ElementNotFound extends Exception
{
    public static function make(string $selector): static
    {
        return new static("The given selector `{$selector} did not match any elements");
    }
}
