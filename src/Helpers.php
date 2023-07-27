<?php

namespace Spatie\Browsershot;

class Helpers
{
    public static function stringContains($haystack, $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}
