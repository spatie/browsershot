---
title: Connection to a remote chromium/chrome instance
weight: 6
---

If you have a remote endpoint for a running chromium/chrome instance, properly configured with the param --remote-debugging-port, you can connect to it using the method `setRemoteInstance`. You only need to specify it's ip and port (defaults are 127.0.0.1 and 9222 accordingly). If no instance is available at the given endpoint (instance crashed, restarting instance, etc), this will fallback to launching a chromium instance.

```php
Browsershot::url('https://example.com')
   ->setRemoteInstance('1.2.3.4', 9222)
   ...
```
