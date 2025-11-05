<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class RemoteConnectionException extends Exception
{
    public static function make(string $error): self
    {
        return new static("Failed to connect to remote browser instance: `{$error}`");
    }
}
