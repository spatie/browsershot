<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class CouldNotTakeBrowsershot extends Exception
{
    public static function chromeOutputEmpty(string $screenShotPath, array $command = [])
    {
        $command = json_encode($command);

        return new static("For some reason Chrome did not write a file at `{$screenShotPath}`. Command payload: {$command}");
    }

    public static function outputFileDidNotHaveAnExtension(string $path)
    {
        return new static("The given path `{$path}` did not contain an extension. Please append an extension.");
    }
}
