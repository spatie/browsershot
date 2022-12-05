<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class FileDoesNotExistException extends Exception
{
    public function __construct($file)
    {
        parent::__construct("The file `{$file}` does not exist");
    }
}
