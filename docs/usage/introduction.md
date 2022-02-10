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

- [an image](/docs/browsershot/v1/usage)
- [a PDF](/docs/browsershot/v1/usage)
- [HTML](/docs/browsershot/v1/usage)
