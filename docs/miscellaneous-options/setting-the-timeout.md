---
title: Setting the timeout
weight: 17
---

The default timeout of Browsershot is set to 60 seconds. Of course, you can modify this timeout:

```php
Browsershot::url('https://example.com')
    ->timeout(120)
    ->save($pathToImage);
```

If you use Browsershot in conjunction with Docker and encounter unexpected timeout errors, it may be due to the PHP_CLI_SERVER_WORKERS environment variable allowing only one worker. It might be necessary to increase the value of this variable.
