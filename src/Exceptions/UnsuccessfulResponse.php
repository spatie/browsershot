<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class UnsuccessfulResponse extends Exception
{
    public function __construct($url, $code)
    {
        parent::__construct("The given url `{$url}` responds with code {$code}");
    }
}
