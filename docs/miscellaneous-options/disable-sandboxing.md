---
title: Disable sandboxing
weight: 7
---

When running Linux in certain virtualization environments it might need to disable sandboxing.

```php
Browsershot::url('https://example.com')
   ->noSandbox()
   ...
```
