---
title: Setting an arbitrary option
weight: 13
---

You can set any arbitrary options by calling `setOption`:

```php
Browsershot::url('https://example.com')
   ->setOption('landscape', true)
   ->save($pathToImage);
```
