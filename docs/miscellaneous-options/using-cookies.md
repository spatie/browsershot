---
title: Using cookies
weight: 20
---

You can add cookies to the request to the given url:

```php
Browsershot::url('https://example.com')
    ->useCookies(['Cookie-Key' => 'Cookie-Value'])
   ...
```

You can specify the domain to register cookies to, if necessary:

```php
Browsershot::url('https://example.com')
    ->useCookies(['Cookie-Key' => 'Cookie-Value'], 'ui.example.com')
   ...
```
