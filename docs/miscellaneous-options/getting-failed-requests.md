---
title: Getting failed requests
weight: 10
---

To get all failed requests encountered while loading a page call `failedRequests`.

```php
$consoleMessages = Browsershot::url('https://example.com')->failedRequests(); // returns an array
```

The `failedRequests` method return an array of which each item is yet another array with these keys. Image that a page contains a broken image

- `status`: the status code, for a broken image: `404`
- `url`: the url of the broken image: `https://example.com/image.jpg`
