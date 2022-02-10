---
title: Adding extra headers to the navigational request
weight: 2
---

To add custom HTTP headers to a navigational HTTP request, use `extraNavigationHTTPHeaders` like so:

```php
Browsershot::url('https://example.com')
    ->setExtraNavigationHttpHeaders(['Custom-Header-Name' => 'Custom-Header-Value'])
   ...
```

This will add the header to the page you want to render, but those headers will not be added to any external resources that make up that page.
