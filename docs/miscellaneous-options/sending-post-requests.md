---
title: Sending POST requests
weight: 12
---

By default, all requests sent using GET method. You can make POST request to the given url by using the `post` method.
Note: POST request sent using `application/x-www-form-urlencoded` content type.

```php
Browsershot::url('https://example.com')
    ->post(['foo' => 'bar'])
   ...
```
