<?php

use Spatie\Browsershot\Helpers;

it('can determine if a string contains a substring', function (string $haystack, $needle, $expectedResult) {
    expect(Helpers::stringContains($haystack, $needle))->toBe($expectedResult);
})->with([
    ['heyheyfile://', 'file://', true],
    ['http://spatie.be', 'file://', false],
    ['file://hey', 'file://', true],
]);
