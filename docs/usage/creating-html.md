---
title: Creating HTML
weight: 3
---


Browsershot also can get the body of an HTML page after JavaScript has been executed:

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
