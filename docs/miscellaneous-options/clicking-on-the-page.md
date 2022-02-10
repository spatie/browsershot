---
title: Clicking on the page
weight: 5
---

You can specify clicks on the page.

```php
Browsershot::url('https://example.com')
    ->click('#selector1')
    // Right click 5 times on #selector2, each click lasting 200 milliseconds.
    ->click('#selector2', 'right', 5, 200)
```
