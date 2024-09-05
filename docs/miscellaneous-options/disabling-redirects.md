---
title: Disabling redirects
weight: 26
---

To avoid redirects to domains that are not allowed in your environment, or for security reasons you can disable HTTP redirects.

```php
Browsershot::url('http://www.spatie.be')
   ->disableRedirects()
   ...
```
