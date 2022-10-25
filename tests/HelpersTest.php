<?php

use Spatie\Browsershot\Helpers;

it('can determine if a string starts with a substring', function (string $haystack, $needle, $expectedResult) {
    expect(Helpers::stringStartsWith($haystack, $needle))->toBe($expectedResult);
})->with([
    ['https://spatie.be', 'https://', true],
    ['http://spatie.be', 'https://', false],
    ['file://hey', 'file://', true],
    ['https://spatie.be', 'file://', false],
]);

it('can determine if a string starts contains a substring', function (string $haystack, $needle, $expectedResult) {
    expect(Helpers::stringContains($haystack, $needle))->toBe($expectedResult);
})->with([
    ['heyheyfile://', 'file://', true],
    ['http://spatie.be', 'file://', false],
    ['file://hey', 'file://', true],
]);
