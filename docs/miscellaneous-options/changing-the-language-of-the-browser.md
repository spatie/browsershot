---
title: Changing the language of the browser
weight: 3
---

You can use `setEnvironmentOptions` to change the language of the browser.
In order to load a page in a specific language for example.

```php
Browsershot::url('https://example.com')
   ->setEnvironmentOptions([
      'LANG' => 'en-GB',
   ])
   ...
```
