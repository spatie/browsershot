---
title: Getting console output
weight: 9
---

To get all output of the Chrome console call `consoleMessages`.

```php
$consoleMessages = Browsershot::url('https://example.com')->consoleMessages(); // returns an array
```

The `consoleMessages` method return an array of which each item is yet another array with these keys:

- `type`: the type of output (`log`, `error`, ...)
- `message`: the message itself
- `location`: an array with information on where the console message was triggered. In most cases, the `url` key will contain the URL where the message was triggered.
