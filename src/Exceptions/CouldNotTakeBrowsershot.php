<?php

namespace Spatie\Browsershot\Exceptions;

use Exception;

class CouldNotTakeBrowsershot extends Exception
{
    public static function chromeOutputEmpty(string $screenShotPath, string $output, array $command = []): static
    {
        $command = json_encode($command);

        $message = <<<CONSOLE
            For some reason Chrome did not write a file at `{$screenShotPath}`.
            Command
            =======
            {$command}
            Output
            ======
            {$output}
            CONSOLE;

        return new static($message);
    }

    public static function outputFileDidNotHaveAnExtension(string $path): static
    {
        return new static("The given path `{$path}` did not contain an extension. Please append an extension.");
    }
}
