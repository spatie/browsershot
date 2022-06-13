---
title: Using HTTP Authentication
weight: 22
---

You can provide credentials for HTTP authentication:

```php
Browsershot::url('https://example.com')
    ->authenticate('username', 'password')
   ...
```
