---
title: Changing the value of a dropdown
weight: 4
---

You can change the value of a dropdown on the page (you can use this to change form select fields).

```php
Browsershot::url('https://example.com')
    ->selectOption('#selector1', '100')
```

You can combine `selectOption`, `type` and `click` to create a screenshot of a page after submitting a form:

```php
Browsershot::url('https://example.com')
    ->type('#firstName', 'My name')
    ->selectOption('#state', 'MT')
    ->click('#submit')
    ->delay($millisecondsToWait)
    ->save($pathToImage);
```
