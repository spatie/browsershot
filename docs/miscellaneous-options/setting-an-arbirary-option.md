---
title: Setting an arbitrary option
weight: 15
---

You can set any arbitrary options by calling `setOption`:

```php
Browsershot::url('https://example.com')
   ->setOption('landscape', true)
   ->save($pathToImage);
```
