# Convert a webpage to an image

[![Build Status](https://secure.travis-ci.org/freekmurze/browsershot.png)](http://travis-ci.org/freekmurze/geocoder)
[![Total Downloads](https://poser.pugx.org/spatie/browsershot/downloads.svg)](https://packagist.org/packages/spatie/browsershot)
[![Latest Stable Version](https://poser.pugx.org/spatie/browsershot/version.png)](https://packagist.org/packages/spatie/browsershot)
[![License](https://poser.pugx.org/spatie/browsershot/license.png)](https://packagist.org/packages/spatie/browsershot)

The package can convert a webpage to an image. To accomplish this conversion [Phantomjs](http://phantomjs.org/) (included in the project) is used.

This package is used to generate the sitepreviews on the homepage of [spatie.be](http://spatie.be)

## Installation

This package can be installed through Composer.

```js
{
    "require": {
		"spatie/browsershot": "dev-master"
	}
}
```

When using Laravel there is a service provider that you can make use of.

```php

// app/config/app.php

'providers' => [
    '...',
    'Spatie\Browsershot\BrowsershotServiceProvider'
];
```

## Usage

Here is a sample call to create an image of a webpage:

```php
    $browsershot = new Spatie\Browsershot\Browsershot();
    $browsershot
        ->setURL('http://www.arstechnica.com')
        ->setWidth('1024')
        ->setHeight('768')
        ->save('targetdirectory/arstechnica-browsershot.jpg');
```

These methods are provided:

* `setBinPath()`: Specify the path to your own phantomjs-binary.
* `setWidth()`: Set the width of the image (defaults to 640).
* `setHeight()`: Set the height of the image (defaults to 480).
* `setURL()`: Set the URL of the webpage which should be converted to an image
* `save($targetFile)`: Starts the conversion-process. The targetfile should have one of these extensions: png, jpg, jpeg.

