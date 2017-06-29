<?php

namespace Spatie\Browsershot\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function skipIfNotRunningonMacOS()
    {
        if (PHP_OS !== 'Darwin') {
            $this->markTestSkipped("Skipping because not running MacOS");
        }
    }
}