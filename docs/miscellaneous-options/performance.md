---
title: Performance
weight: 13
---


This feature helps to reduce memory when using [data urls](https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/Data_URLs).

```php
Browsershot
    ::html('More data urls exists in this document')
    ->disableCaptureURLs()
;
```