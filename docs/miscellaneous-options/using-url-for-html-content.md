---
title: Using url for html content
weight: 23
---

Using the method *setContentUrl* you can set the base url of the request when using the *html* method. Sometimes you may have relative paths in your code. When passing a html page to puppeteer, you don't have a base url set. So any relative path present in your html content will not fetch correctly.

```php
Browsershot::html('<html>... relative paths to fetch ...</html>')
   ->setContentUrl('https://www.example.com')
   ...
```
