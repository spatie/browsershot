---
title: Adding extra headers to every request
weight: 1
---

To add custom HTTP headers to the navigational HTTP request and all resources that make up the page, use `setExtraHttpHeaders`:

```php
Browsershot::url('https://example.com')
    ->setExtraHttpHeaders(['Custom-Header-Name' => 'Custom-Header-Value'])
   ...
```
