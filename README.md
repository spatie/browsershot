# Convert a webpage to an image

[![Latest Version](https://img.shields.io/github/release/spatie/browsershot.svg?style=flat-square)](https://github.com/spatie/browsershot/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/browsershot/master.svg?style=flat-square)](https://travis-ci.org/spatie/browsershot)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/browsershot.svg?style=flat-square)](https://packagist.org/packages/spatie/browsershot)


The package can convert a webpage to an image. To accomplish this conversion [Phantomjs](http://phantomjs.org/) (included in the project) is used.

This package is used to generate the sitepreviews on the homepage of [spatie.be](https://spatie.be). It is also used by [Gordon Murray](https://twitter.com/murrion) to [add previews to shared content](http://www.murrion.com/2015/02/how-i-automate-sharing-content-to-linkedin-using-ayliens-content-analysis-api-and-browsershot/).

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Installation

This package can be installed through Composer.

```bash
composer require spatie/browsershot
```

When using Laravel there is a service provider that you can make use of.

```php

// app/config/app.php

'providers' => [
    '...',
    'Spatie\Browsershot\BrowsershotServiceProvider'
];
```

Please note that the provided binary is intented for use on Ubuntu.

## Usage

Here is a sample call to create an image of a webpage:

```php
    $browsershot = new Spatie\Browsershot\Browsershot();
    $browsershot
        ->setURL('http://www.arstechnica.com')
        ->setWidth(1024)
        ->setHeight(768)
        ->setTimeout(5000)
        ->save('targetdirectory/arstechnica-browsershot.jpg');
```

These methods are provided:

* `setBinPath()`: Specify the path to your own phantomjs-binary.
* `setWidth()`: Set the width of the image (defaults to 640).
* `setHeight()`: Set the height of the image (defaults to 480).
* `setQuality()`: Set the quality of the image (defaults to 60).
* `setHeightToRenderWholePage()`: Calling this method will result in the entire webpage being rendered.
* `setURL()`: Set the URL of the webpage which should be converted to an image
* `setTimeout()`: Set the browsershot timeout duration in ms required to fully load all page assets and scripts (defaults to 5000).
* `setBackgroundColor($hexValueOrColorName)`: Set the background color of the html document prior to taking a screenshot.
* `save($targetFile)`: Starts the conversion-process. The targetfile should have one of these extensions: png, jpg, jpeg.

## Other implementations

- [Node.js](https://github.com/brenden/node-webshot)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


