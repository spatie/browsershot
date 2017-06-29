<?php

namespace Spatie\Browsershot;

use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Browsershot
{
    protected $url = '';

    protected $pathToChrome = '';

    public static function url(string $url)
    {
        return new static($url);
    }

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function save(string $path)
    {
        $temporaryDirectory = (new TemporaryDirectory(sys_get_temp_dir() . DIRECTORY_SEPARATOR . rand()))
            ->create();

        $process = $this->buildScreenshotProcess($temporaryDirectory->path());

        $process->run();

        var_dump($process->getOutput(), $process->getExitCodeText(), $process->getErrorOutput());

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $temporaryDirectory->delete();
    }

    public function setChromePath(string $pathToChrome)
    {
        if (! file_exists($pathToChrome)) {
            CouldNotTakeBrowsershot::chromeNotFound($pathToChrome);
        }

        $this->pathToChrome = $pathToChrome;
    }

    protected function buildScreenshotProcess(string $workingDirectory): Process
    {
        $command = "cd '{$workingDirectory}';'{$this->findChrome()}' --headless --disable-gpu --screenshot {$this->url}";

        return new Process($command);
    }

    protected function findChrome(): string
    {
        if (! empty($this->pathToChrome)) {
            return $this->pathToChrome;
        }

        return ChromeFinder::forCurrentOs();
    }
}