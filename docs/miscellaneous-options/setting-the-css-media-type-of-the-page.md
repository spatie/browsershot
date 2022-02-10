---
title: Setting the CSS media type of the page
weight: 14
---

You can emulate the media type, especially useful when you're generating pdf shots, because it will try to emulate the print version of the page by default.

```php
Browsershot::url('https://example.com')
    ->emulateMedia('screen') // "screen", "print" (default) or null (passing null disables the emulation).
    ->savePdf($pathToPdf);
```

