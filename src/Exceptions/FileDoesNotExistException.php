<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class FileDoesNotExistException extends Exception
{
    public static function make(string $file): static
    {
        return new static("The file `{$file}` does not exist");
    }
}
