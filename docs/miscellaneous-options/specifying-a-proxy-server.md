---
title: Specifying-a-proxy-server
weight: 17
---

You can specify a proxy server to use when connecting. The argument passed to `setProxyServer` will be passed to the `--proxy-server=` option of Chromium. More info here: https://www.chromium.org/developers/design-documents/network-settings#TOC-Command-line-options-for-proxy-settings

```php
Browsershot::url('https://example.com')
   ->setProxyServer("1.2.3.4:8080")
   ...
```
