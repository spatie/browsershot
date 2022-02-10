---
title: Passing environment variables to the browser
weight: 10
---

If you want to set custom environment variables which affect the browser instance you can use:

```php
Browsershot::url('https://example.com')
   ->setEnvironmentOptions(['TZ' => 'Pacific/Auckland'])
   ...
```
