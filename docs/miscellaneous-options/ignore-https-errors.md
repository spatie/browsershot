---
title: Ignore HTTPS errors
weight: 9
---

You can ignore HTTPS errors, if necessary.

```php
Browsershot::url('https://example.com')
   ->ignoreHttpsErrors()
   ...
```
