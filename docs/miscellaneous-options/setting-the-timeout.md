---
title: Setting the timeout
weight: 15
---

The default timeout of Browsershot is set to 60 seconds. Of course, you can modify this timeout:

```php
Browsershot::url('https://example.com')
    ->timeout(120)
    ->save($pathToImage);
```
