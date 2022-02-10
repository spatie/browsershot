---
title: Creating images
weight: 2
---

Here's the easiest way to create an image of a webpage:

```php
Browsershot::url('https://example.com')->save($pathToImage);
```

## Formatting the image

By default, the screenshot's type will be a `png`. (According to [Puppeteer's Config](https://github.com/GoogleChrome/puppeteer/blob/master/docs/api.md#pagescreenshotoptions))
But you can change it to `jpeg` with quality option.

```php
Browsershot::url('https://example.com')
    ->setScreenshotType('jpeg', 100)
    ->save($pathToImage);
```

## Sizing the image

By default the screenshot's size will match the resolution you use for your desktop. Want another size of screenshot? No problem!

```php
Browsershot::url('https://example.com')
    ->windowSize(640, 480)
    ->save($pathToImage);
```

You can also set the size of the output image independently of the size of window. Here's how to resize a screenshot take with a resolution of 1920x1080 and scale that down to something that fits inside 200x200.

```php
Browsershot::url('https://example.com')
    ->windowSize(1920, 1080)
    ->fit(Manipulations::FIT_CONTAIN, 200, 200)
    ->save($pathToImage);
```

You can screenshot only a portion of the page by using `clip`.

```php
Browsershot::url('https://example.com')
    ->clip($x, $y, $width, $height)
    ->save($pathToImage);
```

You can take a screenshot of an element matching a selector using `select` and an optional `$selectorIndex` which is used to select the nth element (e.g. use `$selectorIndex = 3` to get the fourth element like `div:eq(3)`). By default `$selectorIndex` is `0` which represents the first matching element.

```php
Browsershot::url('https://example.com')
    ->select('.some-selector', $selectorIndex)
    ->save($pathToImage);
```

### Getting a screenshot as base64

If you need the base64 version of a screenshot you can use the `base64Screenshot` method. This can come in handy when you don't want to save the screenshot on disk.

```php
$base64Data = Browsershot::url('https://example.com')
    ->base64Screenshot();
```

## Manipulating the image

You can use all the methods [spatie/image](https://docs.spatie.be/image/v1) provides. Here's an example where we create a greyscale image:

```php
Browsershot::url('https://example.com')
    ->windowSize(640, 480)
    ->greyscale()
    ->save($pathToImage);
```

## Taking a full page screenshot

You can take a screenshot of the full length of the page by using `fullPage()`.

```php
Browsershot::url('https://example.com')
    ->fullPage()
    ->save($pathToImage);
```

## Setting the device scale

You can also capture the webpage at higher pixel densities by passing a device scale factor value of 2 or 3. This mimics how the webpage would be displayed on a retina/xhdpi display.

```php
Browsershot::url('https://example.com')
    ->deviceScaleFactor(2)
    ->save($pathToImage);
```

## Mobile emulation

You can emulate a mobile view with the `mobile` and `touch` methods.
`mobile` will set the display to take into account the page's meta viewport, as Chrome mobile would.
`touch` will set the browser to emulate touch functionality, hence allowing spoofing for pages that check for touch.
Along with the `userAgent` method, these can be used to effectively take a mobile screenshot of the page.

```php
Browsershot::url('https://example.com')
    ->userAgent('My Mobile Browser 1.0')
    ->mobile()
    ->touch()
    ->save($pathToImage);
```

## Device emulation

You can emulate a device view with the `device` method. The devices' names can be found [Here](https://github.com/puppeteer/puppeteer/blob/main/src/common/DeviceDescriptors.ts).

```php
$browsershot = new Browsershot('https://example.com', true);
$browsershot
        ->device('iPhone X')
        ->save($pathToImage);
```

is the same as

```php
Browsershot::url('https://example.com')
    ->userAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1')
    ->windowSize(375, 812)
    ->deviceScaleFactor(3)
    ->mobile()
    ->touch()
    ->landscape(false)
    ->save($pathToImage);
```

## Backgrounds

If you want to ignore the website's background when capturing a screenshot, use the `hideBackground()` method.

```php
Browsershot::url('https://example.com')
    ->hideBackground()
    ->save($pathToImage);
```

## Dismiss dialogs

Javascript pop ups such as alerts, prompts and confirmations cause rendering of the site to stop, which leads to an empty screenshot. Calling `dismissDialogs()` method automatically closes such popups allowing the  screenshot to be taken.

```php
Browsershot::url('https://example.com')
    ->dismissDialogs()
    ->save($pathToImage);
```

## Disable Javascript

If you want to completely disable javascript when capturing the page, use the `disableJavascript()` method.
Be aware that some sites will not render correctly without javascript.

```php
Browsershot::url('https://example.com')
    ->disableJavascript()
    ->save($pathToImage);
```

## Disable Images

You can completely remove all images and <img> elements when capturing a page using the `disableImages()` method.

```php
Browsershot::url('https://example.com')
    ->disableImages()
    ->save($pathToImage);
```

## Block Urls

You can completely block connections to specific Urls using the `blockUrls()` method.
Useful to block advertisements and trackers to make screenshot creation faster.

```php
$urlsList = array("example.com/cm-notify?pi=outbrain", "sync.outbrain.com/cookie-sync?p=bidswitch");
Browsershot::url('https://example.com')
    ->blockUrls($urlsList)
    ->save($pathToImage);
```

## Block Domains

You can completely block connections to specific domains using the `blockDomains()` method.
Useful to block advertisements and trackers to make screenshot creation faster.

```php
$domainsList = array("googletagmanager.com", "googlesyndication.com", "doubleclick.net", "google-analytics.com");
Browsershot::url('https://example.com')
    ->blockDomains($domainsList)
    ->save($pathToImage);
```

## Waiting for lazy-loaded resources

Some websites lazy-load additional resources via ajax or use webfonts, which might not be loaded in time for the screenshot. Using the `waitUntilNetworkIdle()` method you can tell Browsershot to wait for a period of 500 ms with no network activity before taking the screenshot, ensuring all additional resources are loaded.

```php
Browsershot::url('https://example.com')
    ->waitUntilNetworkIdle()
    ->save($pathToImage);
```

Alternatively you can use less strict `waitUntilNetworkIdle(false)`, which allows 2 network connections in the 500 ms waiting period, useful for websites with scripts periodically pinging an ajax endpoint.

## Delayed screenshots

You can delay the taking of screenshot by  `setDelay()`. This is useful if you need to wait for completion of javascript or if you are attempting to capture lazy-loaded resources.

```php
Browsershot::url('https://example.com')
    ->setDelay($delayInMilliseconds)
    ->save($pathToImage);
```

## Waiting for javascript function

You can also wait for a javascript function until is returns true by using `waitForFunction()`. This is useful if you need to wait for task on javascript which is not related to network status.

```php
Browsershot::url('https://example.com')
    ->waitForFunction('window.innerWidth < 100', $pollingInMilliseconds, $timeoutInMilliseconds)
    ->save($pathToImage);
```
## Adding JS

You can add javascript prior to your screenshot or output using the syntax for [Puppeteer's addScriptTag](https://github.com/GoogleChrome/puppeteer/blob/v1.9.0/docs/api.md#pageaddscripttagoptions).

```php
Browsershot::url('https://example.com')
    ->setOption('addScriptTag', json_encode(['content' => 'alert("Hello World")']))
    ->save($pathToImage);
```

## Adding CSS

You can add CSS styles prior to your screenshot or output using the syntax for [Puppeteer's addStyleTag](https://github.com/GoogleChrome/puppeteer/blob/v1.9.0/docs/api.md#pageaddstyletagoptions).

```php
Browsershot::url('https://example.com')
    ->setOption('addStyleTag', json_encode(['content' => 'body{ font-size: 14px; }']))
    ->save($pathToImage);
```

## Output directly to the browser

You can output the image directly to the browser using the `screenshot()` method.

```php
$image = Browsershot::url('https://example.com')->screenshot()
```

## Setting the user data directory

You can set the [user data directory](https://chromium.googlesource.com/chromium/src/+/refs/heads/main/docs/user_data_dir.md) that is used to store the browser session and additional data. Setting this to a static value may introduce cache problems, could also increase performance. It needs to be an absolute path.

```php
$image = Browsershot::url('https://example.com')
    ->userDataDir('/tmp/session-1')
    ->screenshot()
```
