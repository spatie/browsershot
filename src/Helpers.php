<?php

namespace Spatie\Browsershot;

class Helpers
{
    public static function stringStartsWith($haystack, $needle): bool
    {
        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    public static function stringContains($haystack, $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}
