---
title: Read redirect history
weight: 26
---

Sometimes it can happen that the screenshot made by Browsershot is different from the one displayed by your browser.
It is often due to one or more redirects made by the site, based on geolocation, cookies, etc...

To understand what happens, you can keep track of the redirects made by the site, both HTTP, Javascript and HTML ones.
In addition to the URLs there are also available HTTP status, reason and headers.

```php
$redirects = Browsershot::url('https://www.spatie.be')
    ->redirectHistory();

foreach ($redirects as $redirect) {
    print $redirect['url'] . PHP_EOL;
    print $redirect['status'] . PHP_EOL;
    print $redirect['reason'] . PHP_EOL;
    foreach ($redirect['headers'] as $name => $value) {
      print $name . ' : ' . $value . PHP_EOL;
    }
    print PHP_EOL;
}
```