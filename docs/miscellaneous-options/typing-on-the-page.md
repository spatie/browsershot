---
title: Typing on the page
weight: 18
---

You can type on the page (you can use this to fill form fields).

```php
Browsershot::url('https://example.com')
    ->type('#selector1', 'Hello, is it me you are looking for?')
```

You can combine `type` and `click` to create a screenshot of a page after submitting a form:

```php
Browsershot::url('https://example.com')
    ->type('#firstName', 'My name')
    ->click('#submit')
    ->delay($millisecondsToWait)
    ->save($pathToImage);
```
