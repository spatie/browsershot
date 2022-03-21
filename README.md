
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

<p align="center"><img src="/art/socialcard.png" alt="Social Card of Spatie's Browsershot"></p>

# Convert a webpage to an image or pdf using headless Chrome

[![Latest Version](https://img.shields.io/github/release/spatie/browsershot.svg?style=flat-square)](https://github.com/spatie/browsershot/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![run-tests](https://github.com/spatie/browsershot/workflows/run-tests/badge.svg)](https://github.com/spatie/browsershot/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/browsershot.svg?style=flat-square)](https://packagist.org/packages/spatie/browsershot)

The package can convert a webpage to an image or pdf. The conversion is done behind the scenes by [Puppeteer](https://github.com/GoogleChrome/puppeteer) which controls a headless version of Google Chrome.

Here's a quick example:

```php
use Spatie\Browsershot\Browsershot;

// an image will be saved
Browsershot::url('https://example.com')->save($pathToImage);
```

It will save a pdf if the path passed to the `save` method has a `pdf` extension.

```php
// a pdf will be saved
Browsershot::url('https://example.com')->save('example.pdf');
```

You can also use an arbitrary html input, simply replace the `url` method with `html`:

```php
Browsershot::html('<h1>Hello world!!</h1>')->save('example.pdf');
```

Browsershot also can get the body of an html page after JavaScript has been executed:

```php
Browsershot::url('https://example.com')->bodyHtml(); // returns the html of the body
```

If you wish to retrieve an array list with all of the requests that the page triggered you can do so:

```php
$requests = Browsershot::url('https://example.com')
    ->triggeredRequests();

foreach ($requests as $request) {
    $url = $request['url']; //https://example.com/
}
```

## Support us

Learn how to create a package like this one, by watching our premium video course:

[![Laravel Package training](https://spatie.be/github/package-training.jpg)](https://laravelpackage.training)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/browsershot).

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Alternatives

If you're not able to install Node and Puppeteer, take a look at [v2 of browsershot](https://github.com/spatie/browsershot/tree/2.4.1), which uses Chrome headless CLI to take a screenshot. `v2` is not maintained anymore, but should work pretty well.

If using headless Chrome does not work for you take a look at at `v1` of this package which uses the abandoned `PhantomJS` binary.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

And a special thanks to [Caneco](https://twitter.com/caneco) for the logo ✨

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
