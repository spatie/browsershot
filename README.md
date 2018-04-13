# Convert a webpage to an image or pdf using headless Chrome

[![Latest Version](https://img.shields.io/github/release/spatie/browsershot.svg?style=flat-square)](https://github.com/spatie/browsershot/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/browsershot/master.svg?style=flat-square)](https://travis-ci.org/spatie/browsershot)
[![StyleCI](https://styleci.io/repos/19386515/shield?branch=master)](https://styleci.io/repos/19386515)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/9c1184fb-1edb-41d5-9d30-2620d99447c7.svg?style=flat-square)](https://insight.sensiolabs.com/projects/9c1184fb-1edb-41d5-9d30-2620d99447c7)
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

Spatie is a webdesign agency in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Requirements

This package requires node 7.6.0 or higher and the Puppeteer Node library.

On MacOS you can install Puppeteer in your project via NPM:

```bash
npm install puppeteer
```

Or you could opt to just install it globally

```bash
npm install puppeteer --global
```

On a [Forge](https://forge.laravel.com) provisioned Ubuntu 16.04 server you can install the latest stable version of Chrome like this:

```bash
curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget
sudo npm install --global --unsafe-perm puppeteer
sudo chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium
```

### Custom node and npm binaries

Depending on your setup, node or npm might be not directly available to Browsershot.
If you need to manually set these binary paths, you can do this by calling the `setNodeBinary` and `setNpmBinary` method.

```
Browsershot::html('Foo')
    ->setNodeBinary('/usr/local/bin/node')
    ->setNpmBinary('/usr/local/bin/npm');
```

By default, Browsershot will use `node` and `npm` to execute commands.

### Custom include path

If you don't want to manually specify binary paths, but rather modify the include path in general,
you can set it using the `setIncludePath` method.

```php
Browsershot::html('Foo')
    ->setIncludePath('$PATH:/usr/local/bin')
```

Setting the include path can be useful in cases where `node` and `npm` can not be found automatically.

### Custom node module path

If you want to use an alternative `node_modules` source you can set it using the `setNodeModulePath` method.

```php
Browsershot::html('Foo')
  ->setNodeModulePath("/path/to/my/project/node_modules/")
```

### Custom binary path

If you want to use an alternative script source you can set it using the `setBinPath` method.

```php
Browsershot::html('Foo')
  ->setBinPath("/path/to/my/project/my_script.js")
```

### Custom chrome/chromium executable path

If you want to use an alternative chrome or chromium executable from what is installed by puppeteer you can set it using the `setChromePath` method.

```php
Browsershot::html('Foo')
  ->setChromePath("/path/to/my/chrome")
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

### Screenshots

Here's the easiest way to create an image of a webpage:

```php
Browsershot::url('https://example.com')->save($pathToImage);
```

#### Formatting the image
By default the screenshot's type will be a `png`. (According to [Puppeteer's Config](https://github.com/GoogleChrome/puppeteer/blob/master/docs/api.md#pagescreenshotoptions))  
But you can change it to `jpeg` with quality option.

```php
Browsershot::url('https://example.com')
    ->setScreenshotType('jpeg', 100)
    ->save($pathToImage);
```

#### Sizing the image

By default the screenshot's size will match the resolution you use for your desktop. Want another size of screenshot? No problem!

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

You can screenshot only a portion of the page by using `clip`.

```php
Browsershot::url('https://example.com')
    ->clip($x, $y, $width, $height)
    ->save($pathToImage);
```

You can take a screenshot of an element matching a selector using `select`.

```php
Browsershot::url('https://example.com')
    ->select('.some-selector')
    ->save($pathToImage);
```

#### Manipulating the image

You can use all the methods [spatie/image](https://docs.spatie.be/image/v1) provides. Here's an example where we create a greyscale image:

```php
Browsershot::url('https://example.com')
    ->windowSize(640, 480)
    ->greyscale()
    ->save($pathToImage);
```


#### Taking a full page screenshot

You can take a screenshot of the full length of the page by using `fullPage()`.

```php
Browsershot::url('https://example.com')
    ->fullPage()
    ->save($pathToImage);
```

#### Setting the device scale
You can also capture the webpage at higher pixel densities by passing a device scale factor value of 2 or 3. This mimics how the webpage would be displayed on a retina/xhdpi display.

```php
Browsershot::url('https://example.com')
    ->deviceScaleFactor(2)
    ->save($pathToImage);
```

#### Mobile emulation

You can emulate a mobile view with the `mobile` and `touch` methods.
`mobile` will set the display to take into account the page's meta viewport, as Chrome mobile would.
`touch` will set the browser to emulate touch functionality, hence allowing spoofing for pages that check for touch.
Along with the `userAgent` method, these can be used to effectively take a mobile screenshot of the page.

```php
Browsershot::url('https://example.com')
    ->userAgent('My Mobile Browser 1.0')
    ->mobile()
    ->touch()
    ->save($pathToImage);
```

#### Backgrounds
If you want to ignore the website's background when capturing a screenshot, use the `hideBackground()` method.

```php
Browsershot::url('https://example.com')
    ->hideBackground()
    ->save($pathToImage);
```

#### Dismiss dialogs
Javascript pop ups such as alerts, prompts and confirmations cause rendering of the site to stop, which leads to an empty screenshot. Calling `dismissDialogs()` method automatically closes such popups allowing the  screenshot to be taken.

```php
Browsershot::url('https://example.com')
    ->dismissDialogs()
    ->save($pathToImage);
```

#### Waiting for lazy-loaded resources
Some websites lazy-load additional resources via ajax or use webfonts, which might not be loaded in time for the screenshot. Using the `waitUntilNetworkIdle()` method you can tell Browsershot to wait for a period of 500 ms with no network activity before taking the screenshot, ensuring all additional resources are loaded.

```php
Browsershot::url('https://example.com')
    ->waitUntilNetworkIdle()
    ->save($pathToImage);
```

Alternatively you can use less strict `waitUntilNetworkIdle(false)`, which allows 2 network connections in the 500 ms waiting period, useful for websites with scripts periodically pinging an ajax endpoint.

#### Delayed screenshots
You can delay the taking of screenshot by  `setDelay()`. This is useful if you need to wait for completion of javascript or if you are attempting to capture lazy-loaded resources.

```php
Browsershot::url('https://example.com')
    ->setDelay($delayInMilliseconds)
    ->save($pathToImage);
```

#### Output directly to the browser
You can output the image directly to the browser using the `screenshot()` method.

```php
$image = Browsershot::url('https://example.com')
    ->screenshot()
```

### PDFs

Browsershot will save a pdf if the path passed to the `save` method has a `pdf` extension.

```php
// a pdf will be saved
Browsershot::url('https://example.com')->save('example.pdf');
```

Alternatively you can explicitly use the `savePdf` method:

```php
Browsershot::url('https://example.com')->savePdf('example.pdf');
```

You can also pass some html which will be converted to a pdf.

```php
Browsershot::html($someHtml)->savePdf('example.pdf');
```

#### Sizing the pdf

You can specify the width and the height in millimeters

```php
Browsershot::html($someHtml)
   ->paperSize($width, $height)
   ->save('example.pdf');
```

#### Setting margins

Margins can be set in millimeters.

```php
Browsershot::html($someHtml)
   ->margins($top, $right, $bottom, $left)
   ->save('example.pdf');
```

#### Headers and footers

By default a PDF will not show the header and a footer generated by Chrome. Here's how you can make the header and footer appear. You can also provide a custom HTML template for the header and footer.

```php
Browsershot::html($someHtml)
   ->showBrowserHeaderAndFooter()
   ->headerHtml($someHtml)
   ->footerHtml($someHtml)
   ->save('example.pdf');
```

In the header and footer HTML, any tags with the following classes will have its printing value injected into its contents.

* `date` formatted print date
* `title` document title
* `url` document location
* `pageNumber` current page number
* `totalPages` total pages in the document

To hide the header or footer, you can call either `hideHeader` or `hideFooter`.

#### Backgrounds

By default, the resulting PDF will not show the background of the html page. If you do want the background to be included you can call `showBackground`:

```php
Browsershot::html($someHtml)
   ->showBackground()
   ->save('example.pdf');
```

#### Landscape orientation

Call `landscape` if you want to resulting pdf to be landscape oriented.

```php
Browsershot::html($someHtml)
   ->landscape()
   ->save('example.pdf');
```

#### Only export specific pages

You can control which pages should be export by passing a print range to the `pages` method.  Here are some examples of valid print ranges: `1`, `1-3`,  `1-5, 8, 11-13`.

```php
Browsershot::html($someHtml)
   ->pages('1-5, 8, 11-13')
   ->save('example.pdf');
```

#### Output directly to the browser
You can output the PDF directly to the browser using the `pdf()` method.

```php
$pdf = Browsershot::url('https://example.com')
    ->pdf()
```

### HTML

Browsershot also can get the body of an html page after JavaScript has been executed:

```php
Browsershot::url('https://example.com')->bodyHtml(); // returns the html of the body
```

### Evaluate

Browsershot can get the evaluation of an html page:

```php
Browsershot::url('https://example.com')
  ->deviceScaleFactor(2)
  ->evaluate("window.devicePixelRatio"); // returns 2
```

### Misc

#### Setting an arbitrary option

You can set any arbitrary options by calling `setOption`:

```php
Browsershot::url('https://example.com')
   ->setOption('landscape', true)
   ->save($pathToImage);
```

#### Fixing cors issues

If you experience issues related to [cors](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS), you can opt to disable cors checks with [--disable-web-security](https://peter.sh/experiments/chromium-command-line-switches/#disable-web-security).

```php
Browsershot::url('https://example.com')
   ->setOption('args', ['--disable-web-security'])
   ->save($pathToImage);
```

#### Changing the language of the browser

You can use `setOption` to change the language of the browser.  
In order to load a page in a specific language for example.

```php
Browsershot::url('https://example.com')
   ->setOption('args', '--lang=en-GB')
   ...
```

#### Setting the user agent


If, for some reason, you want to set the user agent Google Chrome should use when taking the screenshot you can do so:

```php
Browsershot::url('https://example.com')
    ->userAgent('My Special Snowflake Browser 1.0')
    ->save($pathToImage);
```

#### Setting the CSS media type of the page


You can also emulate the media type, especially usefull when you're generating pdf shots, because it will try to emulate the print version of the page by default.

```php
Browsershot::url('https://example.com')
    ->emulateMedia('screen') // "screen", "print" (default) or null (passing null disables the emulation).
    ->savePdf($pathToPdf);
```


The default timeout of Browsershot is set to 60 seconds. Of course, you can modify this timeout:

```php
Browsershot::url('https://example.com')
    ->timeout(120)
    ->save($pathToImage);
```

#### Disable sandboxing

When running Linux in certain virtualization enviroments it might need to disable sandboxing.

```php
Browsershot::url('https://example.com')
   ->noSandbox()
   ...
```

#### Ignore HTTPS errors

You can ignore HTTPS errors, if necessary.

```php
Browsershot::url('https://example.com')
   ->ignoreHttpsErrors()
   ...
```

#### Specify a proxy Server

You can specify a proxy server to use when connecting. The argument passed to `setProxyServer` will be passed to the `--proxy-server=` option of Chromium. More info here: https://www.chromium.org/developers/design-documents/network-settings#TOC-Command-line-options-for-proxy-settings

```php
Browsershot::url('https://example.com')
   ->setProxyServer("1.2.3.4:8080")
   ...
```

#### Setting extraHTTPHeaders

To send custom HTTP headers, set the extraHTTPHeaders option like so:

```php
Browsershot::url('https://example.com')
    ->setExtraHttpHeaders(['Custom-Header-Name' => 'Custom-Header-Value'])
   ...
```


#### Clicking on the page

You can specify clicks on the page.

```php
Browsershot::url('https://example.com')
    ->click('#selector1')
    ->click('#selector2', 'right', 5, 200) // Right click 5 times on #selector2, each click lasting 200 milliseconds.
```

## Related packages

* Laravel wrapper: [laravel-browsershot](https://github.com/verumconsilium/laravel-browsershot)


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Alternatives

If you're not able to install Node and Puppeteer, take a look at [v2 of browserhot](https://github.com/spatie/browsershot/tree/2.4.1), which uses Chrome headless CLI to take a screenshot. `v2` is not maintained anymore, but should work pretty well.

If using headless Chrome does not work for you take a lookat at `v1` of this package which uses the abandoned `PhantomJS` binary.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie).
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
