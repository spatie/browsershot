---
title: Introduction
weight: 1
---

The package can convert a webpage to an image or pdf. The conversion is done behind the scenes by [Puppeteer](https://github.com/puppeteer/puppeteer) which controls a headless version of Google Chrome.

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
