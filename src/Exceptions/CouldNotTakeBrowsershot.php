<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class CouldNotTakeBrowsershot extends Exception
{
    public static function chromeOutputEmpty(string $screenShotPath)
    {
        return new static("For some reason Chrome did not write a file at `{$screenShotPath}`.");
    }

    public static function outputFileDidNotHaveAnExtension(string $path)
    {
        return new static("The given path `{$path}` did not contain an extension. Please append an extension.");
    }

    public static function postParamsArrayIsNotAssoc()
    {
        return new static('The given postParams array is not associative.');
    }

    public static function urlHasNoScheme()
    {
        return new static('The given url did not contain scheme.');
    }

    public static function postRequestsNotSupportedByResource()
    {
        return new static('POST requests not supported by given resource.');
    }
}
