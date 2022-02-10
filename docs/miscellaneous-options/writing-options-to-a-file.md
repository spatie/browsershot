---
title: Writing options to a file
weight: 21
---

When the amount of options given to puppeteer becomes too big, Browsershot will fail because of an overflow of characters in the command line.
Browsershot can write the options to a file and pass that file to puppeteer and so bypass the character overflow.

```php
Browsershot::url('https://example.com')
   ->writeOptionsToFile()
   ...
```
