<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class UnsuccessfulResponse extends Exception
{
    public static function make(string $url, string|int $code): static
    {
        return new static("The given url `{$url}` responds with code {$code}");
    }
}
