# Convert a webpage to an image or pdf using headless Chrome

[![Latest Version](https://img.shields.io/github/release/spatie/browsershot.svg?style=flat-square)](https://github.com/spatie/browsershot/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/browsershot/master.svg?style=flat-square)](https://travis-ci.org/spatie/browsershot)
[![StyleCI](https://styleci.io/repos/19386515/shield?branch=master)](https://styleci.io/repos/19386515)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9c1184fb-1edb-41d5-9d30-2620d99447c7.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9c1184fb-1edb-41d5-9d30-2620d99447c7)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/browsershot.svg?style=flat-square)](https://packagist.org/packages/spatie/browsershot)

The package can convert a webpage to an image or pdf. The conversion is done behind the screens by Google Chrome.

Here's a quick example:

```php
use Spatie\Browsershot\Browsershot;

Browsershot::url('https://example.com')->save($pathToImage);
```

It will save a pdf if the path passed to the `save` method has a `pdf` extension.

```php
// a pdf will be saved
Browsershot::url('https://example.com')->save('example.pdf');
```

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/postcards).

## Requirements

This package has been tested on MacOS and Ubuntu 16.04. If you use another OS your mileage may vary. Chrome 59 or higher should be installed on your system.

On a [Forge](https://forge.laravel.com) provisioned Ubuntu 16.04 server you can install the latest stable version of Chrome like this:

```bash
sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
sudo apt-get update
sudo apt-get -f install
sudo apt-get install google-chrome-stable
```

## Installation

This package can be installed through Composer.

```bash
composer require spatie/browsershot
```

## Usage

In all examples it is assumed that you imported this namespace at the top of your file

```php
use Spatie\Browsershot\Browsershot;
```

Here's the easiest way to create an image of a webpage:

```php
Browsershot::url('https://example.com')->save($pathToImage);
```

Browsershot will make an educated guess where Google Chrome is located. If Chrome can not be found on your system you can manually set its location:

```php
Browsershot::url('https://example.com')
   ->setChromePath($pathToChrome)
   ->save($pathToImage);
```

By default the screenshot will be a `png` and it's size will match the resolution you use for your desktop. Want another size of screenshot? No problem!

```php
Browsershot::url('https://example.com')
    ->windowSize(640, 480)
    ->save($pathToImage);
```

You can also set the size of the output image independently of the size of window. Here's how to resize a screenshot take with a resolution of 1920x1080 and scale that down to something that fits inside 200x200.

```php
Browsershot::url('https://example.com')
    ->windowSize(1920, 1080)
    ->fit(Manipulations::FIT_CONTAIN, 200, 200)
    ->save($pathToImage);
```

In fact, you can use all the methods [spatie/image](https://docs.spatie.be/image/v1) provides. Here's an example where we create a greyscale image:

```php
Browsershot::url('https://example.com')
    ->windowSize(640, 480)
    ->greyscale()
    ->save($pathToImage);
```

If, for some reason, you want to set the user agent Google Chrome should use when taking the screenshot you can do so:

```php
Browsershot::url('https://example.com')
    ->userAgent('My Special Snowflake Browser 1.0')
    ->save($pathToImage);
```

The default timeout of Browsershot is set to 60 seconds. Of course, you can modify this timeout:

```php
Browsershot::url('https://example.com')
    ->timeout(120)
    ->save($pathToImage);
```

Browsershot will save a pdf if the path passed to the `save` method has a `pdf` extension.

```php
// a pdf will be saved
Browsershot::url('https://example.com')->save('example.pdf');
```

Alternatively you can explicitly use the `savePdf` method:

```php
Browsershot::url('https://example.com')->savePdf('example.pdf');
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Alternatives

If you're not able to install Chrome 59 or higher, take a look at [v1 of browserhot](https://github.com/spatie/browsershot/tree/1.9.1), which uses PhanomJS to take a screenshot. `v1` is not maintained anymore, but should work pretty well

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
