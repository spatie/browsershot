<?php

namespace Spatie\Browsershot;

use Composer\InstalledVersions;
use Exception;
use Spatie\Image\Image;

class ImageManipulations
{
    protected array $manipulations = [];

    public function __call(string $method, array $parameters): self
    {
        $this->addManipulation($method, $parameters);

        return $this;
    }

    public function addManipulation(string $name, array $parameters = []): self
    {
        $this->manipulations[$name] = $parameters;

        return $this;
    }

    public function apply(string $path): void
    {
        $this->ensureImageDependencyIsInstalled();

        $image = Image::load($path);

        foreach ($this->manipulations as $manipulationName => $parameters) {
            $image->$manipulationName(...$parameters);
        }

        $image->save($path);
    }

    public function isEmpty(): bool
    {
        return count($this->manipulations) === 0;
    }

    public function ensureImageDependencyIsInstalled(): void
    {
        if (! InstalledVersions::isInstalled('spatie/image')) {
            throw new Exception('The spatie/image package is required to perform image manipulations. Please install it by running `composer require spatie/image`');
        }

        $installedVersion = InstalledVersions::getVersion('spatie/image');

        if (version_compare($installedVersion, '3.0.0', '<')) {
            throw new Exception("The spatie/image package must be at least version 3.0.0 to perform image manipulations. Your current version is `{$installedVersion}`");
        }
    }
}
