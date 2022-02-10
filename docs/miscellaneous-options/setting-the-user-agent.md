---
title: Setting the user agent
weight: 16
---

If you want to set the user agent Google Chrome should use when taking the screenshot you can do so:

```php
Browsershot::url('https://example.com')
    ->userAgent('My Special Snowflake Browser 1.0')
    ->save($pathToImage);
```

