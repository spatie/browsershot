---
title: Introduction
weight: 1
---

In all examples it is assumed that you imported this namespace at the top of your file

```php
use Spatie\Browsershot\Browsershot;
```

Browsershot can read the HTML of a URL and do something with it, for example convert it to pdf

```php
Browsershot::url('https://example.com')->save('example.pdf');
```

Alternatively, you can  use an arbitrary html input, using the `html` function:

```php
Browsershot::html('<h1>Hello world!!</h1>')->save('example.pdf');
```

Here are the things that Browsershot can produce for you:

- [an image](/docs/browsershot/v4/usage)
- [a PDF](/docs/browsershot/v4/usage)
- [HTML](/docs/browsershot/v4/usage)

## Local file access

HTML passed to `html()` or `setHtml()` cannot load local files by default. Browsershot blocks `file:` requests in Chrome, including requests from images, stylesheets, scripts and iframes. This also blocks absolute filesystem paths such as `/var/www/app/public/logo.png`, which resolve to local files when rendering HTML without a content URL, and file URLs constructed by JavaScript.

Blocked resources will not appear in the output. File URLs in HTML are blocked during rendering rather than rejected by `html()` with an exception.

If your trusted HTML intentionally loads local assets, you can explicitly allow local file access:

```php
Browsershot::html('<img src="file:///var/www/app/public/logo.png">')
    ->allowLocalFileAccess()
    ->save('example.pdf');
```

This applies to PDFs, screenshots and HTML evaluation. You can restore the default restriction with `allowLocalFileAccess(false)`.

Only enable this for HTML you trust. It permits access to any local file readable by Chrome, not just assets in your application's public directory. It does not disable the other HTML validation checks.

`htmlFromFilePath()` and `setHtmlFromFilePath()` intentionally render local HTML files and retain their existing local-resource access. `setContentUrl()` still controls how relative paths are resolved, and HTTP(S) resources are not blocked by the local-file restriction. Allowing local file access does not override Chrome's own security restrictions.

Browsershot is not a sandbox for untrusted HTML. You are still responsible for validating HTML and URLs. If you render untrusted content, isolate the browser's filesystem and network access; blocking local files does not prevent requests to internal HTTP services.
