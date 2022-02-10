---
title: Fixing cors issues
weight: 8
---

If you experience issues related to [cors](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS), you can opt to disable cors checks with [--disable-web-security](https://peter.sh/experiments/chromium-command-line-switches/#disable-web-security).

```php
Browsershot::url('https://example.com')
   ->setOption('args', ['--disable-web-security'])
   ->save($pathToImage);
```
