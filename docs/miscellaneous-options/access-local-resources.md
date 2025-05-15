---
title: Access local resources
weight: 28
---

By default, resources from the local network are blocked to prevent Server-Side Request Forgery (SSRF).

If you need Browsershot to access private IP addresses or IPs from reserved ranges, explicitly set the flag:

```php
Browsershot::url('http://127.0.0.1', allowInternalResources: true);
```