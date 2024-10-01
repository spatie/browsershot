---
title: Setting the CSS media type of the page
weight: 16
---

You can emulate the media type, especially useful when you're generating pdf shots, because it will try to emulate the print version of the page by default.

```php
Browsershot::url('https://example.com')
    ->emulateMedia('screen') // "screen", "print" (default) or null (passing null disables the emulation).
    ->savePdf($pathToPdf);
```

You can also emulate [media features](https://www.w3.org/TR/mediaqueries-5/), such as dark mode or reduced motion.

```php
Browsershot::url('https://example.com')
    ->emulateMediaFeatures([
        ['name' => 'prefers-color-scheme', 'value' => 'dark']
    ])
    ->save($pathToImage);
```
