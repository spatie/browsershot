---
title: Prevent unsuccessful responses
weight: 12
---

You may want to throw an error when the page response is unsuccessful, you can use the following method :

```php
Browsershot::url('https://example.com')
   ->preventUnsuccessfulResponse()
    ...
```
