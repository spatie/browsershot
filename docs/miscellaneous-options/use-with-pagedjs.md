---
title: Use paged.js 
weight: 21
---

You can inject the paged.js polyfill library:

```php
Browsershot::url('https://example.com')
    ->usePagedJS()
```

You can also provide a custom version of the script which will be injected as an argument:

```php
Browsershot::url('https://example.com')
    ->usePagedJS('https://unpkg.com/pagedjs@0.2.0/dist/paged.polyfill.js')
```
