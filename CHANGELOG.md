## Changelog

All notable changes to `Browsershot` will be documented in this file

## 3.22.1 - 2018-04-20

- better handling of timeouts

## 3.22.0 - 2018-04-18

- add `evaluate`

## 3.21.0 - 2018-04-13

- add `setScreenshotType`

## 3.20.1 - 2018-04-13

- fix typehint in `emulateMedia`
- drop PHP 7.0 support

## 3.20.0 - 2018-04-13

- add `click`

## 3.19.0 - 2018-04-03

- add `setExtraHttpHeaders`

### 3.18.0 - 2018-03-28

- add support for taking screenshot of an element using a selector

### 3.17.0 - 2018-02-22

- add support for custom binary/browser script

### 3.16.1 - 2018-02-08

- support symfony ^4.0
- support phpunit ^7.0

### 3.16.0 - 2018-01-28

- add `waitUntilNetworkIdle`

### 3.15.0 - 2018-01-20

- add ability to set sustom header and footer

### 3.14.1 - 2017-12-24

- update dep on `spatie/image`

### 3.14.0 - 2017-12-10

- add `setChromePath`

### 3.13.0 - 2017-12-07

- add ability to set node module path
- add ability to output directory to the browser

### 3.12.0 - 2017-11-20

- add `setDelay`

### 3.11.1 - 2017-11-18

- improve error handling for when no extension is provided

### 3.11.0 - 2017-11-16

- add `setOption`
- refactor internals

### 3.10.0 - 2017-11-13

- add `setProxyServer`

### 3.9.0 - 2017-11-13

- add `dismissDialogs`

### 3.8.1 - 2017-11-10

- move snapshot package to dev-deps

### 3.8.0 - 2017-11-07

- allow the use of the 'omitBackground' option when capturing screenshots

### 3.7.0 - 2017-10-31

- add docblocks for static constructors to support IDE autocompletion

### 3.6.0 - 2017-10-31

- make `paperSize` use floats instead of integers, addressing US paper sizes

### 3.5.0 - 2017-10-28
- add `mobile` and `touch` functions

### 3.4.0 - 2017-10-27
- add `ignoreHttpsErrors`

### 3.3.1 - 2017-10-26
- fix default npm path 

### 3.3.0 - 2017-10-25
- add `noSandbox`

### 3.2.1 - 2017-10-25
- fix setting margins

### 3.2.0 - 2017-10-18
- add `setNetworkIdleTimeout`

### 3.1.0 - 2017-10-18
- make node and npm paths configurable
- improved out of the box experience for Laravel Valet users

### 3.0.0 - 2017-10-16
- use Puppeteer to call Chrome
- add various options enabled by using Puppeteer

### 2.4.1 - 2017-09-27
- add the default path for linux Chromium users

### 2.4.0 - 2017-09-21
- add `bodyHtml` method

### 2.3.0 - 2017-09-19
- add high pixel density support

### 2.2.0 - 2017-08-31
- add support for directly converting some html

### 2.1.0 - 2017-08-06
- add support for saving `pdf`s

### 2.0.3 - 2017-07-05
- lower `symfony/process` requirement

### 2.0.2 - 2017-07-05
- security improvements
- clean up unneeded files

### 2.0.1 - 2017-07-04
- add support for urls with special characters

### 2.0.0 - 2017-07-03
- complete rewrite
- ditch PhantomJS is favour of headless Chrome

### 1.9.1 - 2017-06-02
- fix error message

### 1.7.0 - 2017-05-13
- added support MacOS

### 1.8.0 - 2017-04-27
- added support for setting a custom user agent

### 1.8.0 - 2017-04-27
- added support for setting a custom user agent

### 1.7.0 - 2017-03-14
- added support for W w w windows

### 1.6.0 - 2017-01-02
- added support for some more extensions

### 1.5.4 - 2016-12-17
- make `width` protected

### 1.5.3 - 2016-11-12
- Fix width issues

### 1.5.2 - 2016-08-18
- Upgrade `phantomjs` binary

### 1.5.1 - 2016-04-25
- Fixed a bug where phantomjs keeps on rendering until the end of time

### 1.5.0
- Added a method to set the background color

### 1.4.0
- Added timeout parameter

### 1.3.0
- Added quality parameter
