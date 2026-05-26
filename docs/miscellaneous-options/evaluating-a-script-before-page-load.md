---
title: Evaluating a script before page load
weight: 27
---

Sometimes you need JavaScript to run before any of the page's own scripts execute. The `evaluateOnNewDocument` method injects a script that runs before the page loads, on every page Browsershot navigates to. Internally it uses Puppeteer's [`page.evaluateOnNewDocument()`](https://pptr.dev/api/puppeteer.page.evaluateonnewdocument).

This is handy for stubbing globals, injecting polyfills, overriding `navigator.webdriver`, or disabling analytics before navigation.

```php
Browsershot::url('https://example.com')
    ->evaluateOnNewDocument('window.myFlag = true')
    ->save($pathToImage);
```

The argument is evaluated as a script, so pass a statement rather than a function expression. The script above sets `window.myFlag` before any of the page's own scripts run.
