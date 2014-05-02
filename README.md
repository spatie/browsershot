# Convert a webpage to an image

[![Build Status](https://secure.travis-ci.org/freekmurze/browsershot.png)](http://travis-ci.org/freekmurze/geocoder)
[![Latest Stable Version](https://poser.pugx.org/spatie/browsershot/version.png)](https://packagist.org/packages/spatie/browsershot)
[![License](https://poser.pugx.org/spatie/browsershot/license.png)](https://packagist.org/packages/spatie/browsershot)

The package can convert a webpage to an image.

## Installation

You can install this package through Composer.

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