<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=browsershot">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/browsershot/html/dark.webp">
        <img alt="Logo for Browsershot" src="https://spatie.be/packages/header/browsershot/html/light.webp" height="190">
      </picture>
    </a>

<h1>Render web pages to an image or PDF with Puppeteer</h1>
    
[![Latest Version](https://img.shields.io/github/release/spatie/browsershot.svg?style=flat-square)](https://github.com/spatie/browsershot/releases)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![run-tests](https://img.shields.io/github/actions/workflow/status/spatie/browsershot/run-tests.yml?label=tests&style=flat-square)](https://github.com/spatie/browsershot/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/browsershot.svg?style=flat-square)](https://packagist.org/packages/spatie/browsershot)
    
</div>

The package can convert a web page to an image or PDF. The conversion is done behind the scenes by [Puppeteer](https://github.com/GoogleChrome/puppeteer) which runs a headless version of Google Chrome.

Here's a quick example:

```php
use Spatie\Browsershot\Browsershot;

// an image will be saved
Browsershot::url('https://example.com')->save($pathToImage);
```

It will save a PDF if the path passed to the `save` method has a `pdf` extension.

```php
// a pdf will be saved
Browsershot::url('https://example.com')->save('example.pdf');
```

You can also use an arbitrary html input, simply replace the `url` method with `html`:

```php
Browsershot::html('<h1>Hello world!!</h1>')->save('example.pdf');
```

If your HTML input is already in a file locally use the :

```php
Browsershot::htmlFromFilePath('/local/path/to/file.html')->save('example.pdf');
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

To use Chrome's new [headless mode](https://developers.google.com/web/updates/2017/04/headless-chrome) pass the `newHeadless` method:

```php
Browsershot::url('https://example.com')->newHeadless()->save($pathToImage);
```

## Support us

Learn how to create a package like this one, by watching our premium video course:

[![Laravel Package training](https://spatie.be/github/package-training.jpg)](https://laravelpackage.training)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/browsershot).

## Testing

For running the testsuite, you'll need to have Puppeteer installed. Pleaser refer to the Browsershot requirements [here](https://spatie.be/docs/browsershot/v4/requirements). Usually `npm -g i puppeteer` will do the trick.

Additionally, you'll need the `pdftotext` CLI which is part of the poppler-utils package. More info can be found in in the [spatie/pdf-to-text readme](https://github.com/spatie/pdf-to-text?tab=readme-ov-file#requirements). Usually `brew install poppler-utils` will suffice.

Finally run the tests with:

```bash
composer test
```

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
