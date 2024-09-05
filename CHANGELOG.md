## 4.3.1 - 2024-08-30

### What's Changed

* Add check for `file:/` URL fetching by @JaredPage in https://github.com/spatie/browsershot/pull/xyz
* Added the ability to disable redirects via the `disableRedirects` method by @JaredPage in https://github.com/spatie/browsershot/pull/xyz

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.3.0...4.3.1

## 4.3.0 - 2024-08-22

### What's Changed

* Fix empty PDF issue with Puppeteer ^23.0.0  by @JeppeKnockaert in https://github.com/spatie/browsershot/pull/876

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.2.1...4.3.0

## 4.2.1 - 2024-08-20

Revert changes of 4.2.1 because PDFs do not render correctly anymore (see https://github.com/spatie/laravel-pdf/issues/175)

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.2.0...4.2.1

## 4.2.0 - 2024-08-20

### What's Changed

* Add options to set protocol timeout by @zarulizham in https://github.com/spatie/browsershot/pull/865
* Update Laravel Forge instructions.md by @mchev in https://github.com/spatie/browsershot/pull/872
* Correctly respond to disableCaptureURLS option by @bluesheep100 in https://github.com/spatie/browsershot/pull/871
* add disableCaptureURLs() document by @ziaratban in https://github.com/spatie/browsershot/pull/864
* Update handling of browser output for Puppeteer ^23.0.0 by @bluesheep100 in https://github.com/spatie/browsershot/pull/870

### New Contributors

* @zarulizham made their first contribution in https://github.com/spatie/browsershot/pull/865
* @mchev made their first contribution in https://github.com/spatie/browsershot/pull/872
* @bluesheep100 made their first contribution in https://github.com/spatie/browsershot/pull/871

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.1.3...4.2.0

## 4.1.3 - 2024-07-15

### What's Changed

* option to disable the capturing request by @ziaratban in https://github.com/spatie/browsershot/pull/861

### New Contributors

* @ziaratban made their first contribution in https://github.com/spatie/browsershot/pull/861

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.1.2...4.1.3

## 4.1.2 - 2024-07-15

### What's Changed

* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/spatie/browsershot/pull/860
* Fix - Clean up the temporary options file when browser was called successfully by @Ardenexal in https://github.com/spatie/browsershot/pull/863

### New Contributors

* @Ardenexal made their first contribution in https://github.com/spatie/browsershot/pull/863

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.1.1...4.1.2

## 4.1.1 - 2024-07-03

### What's Changed

* Issue with Windows temp path fixed by @SyedMuradAliShah in https://github.com/spatie/browsershot/pull/858

### New Contributors

* @SyedMuradAliShah made their first contribution in https://github.com/spatie/browsershot/pull/858

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.1.0...4.1.1

## 4.1.0 - 2024-06-12

### What's Changed

* Prevent taking screenshots twice by @clementmas in https://github.com/spatie/browsershot/pull/849

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.5...4.1.0

## 4.0.5 - 2024-06-04

### What's Changed

* Fix save method if there are no image manipulations by @moisish in https://github.com/spatie/browsershot/pull/847

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.4...4.0.5

## 4.0.4 - 2024-05-24

### What's Changed

* Fixing `spatie/image`'s dependant bot alerts issues in `composer.lock` by @thanosalexandris in https://github.com/spatie/browsershot/pull/846

### New Contributors

* @thanosalexandris made their first contribution in https://github.com/spatie/browsershot/pull/846

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.3...4.0.4

## 4.0.3 - 2024-05-21

### What's Changed

* Fix redirect history test by @niclas-timm in https://github.com/spatie/browsershot/pull/836
* Bump aglipanci/laravel-pint-action from 2.3.1 to 2.4 by @dependabot in https://github.com/spatie/browsershot/pull/838
* Bump dependabot/fetch-metadata from 2.0.0 to 2.1.0 by @dependabot in https://github.com/spatie/browsershot/pull/841
* Update installation instructions for nodejs + Puppeteer by @benholmen in https://github.com/spatie/browsershot/pull/843
* Fix generation of large PDFs by @clementmas in https://github.com/spatie/browsershot/pull/827

### New Contributors

* @benholmen made their first contribution in https://github.com/spatie/browsershot/pull/843

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.2...4.0.3

## 4.0.2 - 2024-04-01

### What's Changed

* Update node version by @alnutile in https://github.com/spatie/browsershot/pull/830
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/spatie/browsershot/pull/832
* Replace waitForTimeout by @niclas-timm in https://github.com/spatie/browsershot/pull/834

### New Contributors

* @alnutile made their first contribution in https://github.com/spatie/browsershot/pull/830
* @niclas-timm made their first contribution in https://github.com/spatie/browsershot/pull/834

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.1...4.0.2

## 4.0.1 - 2024-01-02

### What's Changed

* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/spatie/browsershot/pull/809
* [Fix] Change `screenshotQuality` property type to integer by @orkhanahmadov in https://github.com/spatie/browsershot/pull/810

**Full Changelog**: https://github.com/spatie/browsershot/compare/4.0.0...4.0.1

## 4.0.0 - 2023-12-29

- modernize codebase
- made dependency on spatie/image optional

## 3.61.0 - 2023-12-21

### What's Changed

* Adding a taggedPdf option by @ntaylor-86 in https://github.com/spatie/browsershot/pull/804

### New Contributors

* @ntaylor-86 made their first contribution in https://github.com/spatie/browsershot/pull/804

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.60.2...3.61.0

## 3.60.2 - 2023-12-18

### What's Changed

* Allow symfony/process 7.x by @thecaliskan in https://github.com/spatie/browsershot/pull/803

### New Contributors

* @thecaliskan made their first contribution in https://github.com/spatie/browsershot/pull/803

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.60.1...3.60.2

## 3.60.1 - 2023-11-28

### What's Changed

* fixing ?? syntax with ternary by @Chetanp23 in https://github.com/spatie/browsershot/pull/795

### New Contributors

* @Chetanp23 made their first contribution in https://github.com/spatie/browsershot/pull/795

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.60.0...3.60.1

## 3.60.0 - 2023-11-16

### What's Changed

- Added getOutput method for full output data access on any request by @vitorsemeano in https://github.com/spatie/browsershot/pull/780

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.59.0...3.60.0

## 3.59.0 - 2023-10-09

### What's Changed

- Update return types by @lamberttraccard in https://github.com/spatie/browsershot/pull/766
- Update requirements.md for Forge provisioning by @colinmackinlay in https://github.com/spatie/browsershot/pull/777
- Test against php 8.3 by @sergiy-petrov in https://github.com/spatie/browsershot/pull/781
- Add waitForSelector() by @shadoWalker89 in https://github.com/spatie/browsershot/pull/715
- Drop support for PHP 7

### New Contributors

- @lamberttraccard made their first contribution in https://github.com/spatie/browsershot/pull/766
- @colinmackinlay made their first contribution in https://github.com/spatie/browsershot/pull/777
- @sergiy-petrov made their first contribution in https://github.com/spatie/browsershot/pull/781

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.58.2...3.59.0

## 3.58.2 - 2023-07-27

### What's Changed

- Let Symfony/process handle the command escape on Windows by @EmanueleCoppola in https://github.com/spatie/browsershot/pull/757
- Cleanup temporary html files by @angelej in https://github.com/spatie/browsershot/pull/735

### New Contributors

- @angelej made their first contribution in https://github.com/spatie/browsershot/pull/735

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.58.1...3.58.2

## 3.58.1 - 2023-07-19

### What's Changed

- Redirect History: Update docs by @bessone in https://github.com/spatie/browsershot/pull/748
- Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot in https://github.com/spatie/browsershot/pull/749
- Fix Windows command escaping by @EmanueleCoppola in https://github.com/spatie/browsershot/pull/756

### New Contributors

- @EmanueleCoppola made their first contribution in https://github.com/spatie/browsershot/pull/756

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.58.0...3.58.1

## 3.58.0 - 2023-06-30

### What's Changed

- Read redirect history by @bessone in https://github.com/spatie/browsershot/pull/747

### New Contributors

- @bessone made their first contribution in https://github.com/spatie/browsershot/pull/747

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.8...3.58.0

## 3.57.8 - 2023-06-28

### What's Changed

- Fix node command error caused by type: module in package.json by @osbre in https://github.com/spatie/browsershot/pull/740

### New Contributors

- @osbre made their first contribution in https://github.com/spatie/browsershot/pull/740

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.7...3.57.8

## 3.57.7 - 2023-06-09

### What's Changed

- A word was missing by @sdebacker in https://github.com/spatie/browsershot/pull/704
- Devices url for puppeteer has changed by @Skullbock in https://github.com/spatie/browsershot/pull/706
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/browsershot/pull/708
- Fix link to Puppeteer devices in creating-images.md by @kriiv in https://github.com/spatie/browsershot/pull/709
- Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot in https://github.com/spatie/browsershot/pull/726
- Update creating-images.md by @adityadees in https://github.com/spatie/browsershot/pull/732
- Bump dependabot/fetch-metadata from 1.4.0 to 1.5.1 by @dependabot in https://github.com/spatie/browsershot/pull/737

### New Contributors

- @sdebacker made their first contribution in https://github.com/spatie/browsershot/pull/704
- @Skullbock made their first contribution in https://github.com/spatie/browsershot/pull/706
- @kriiv made their first contribution in https://github.com/spatie/browsershot/pull/709
- @adityadees made their first contribution in https://github.com/spatie/browsershot/pull/732

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.6...3.57.7

## 3.57.6 - 2023-01-03

### What's Changed

- Docs: Use setEnvironmentOptions to set Browser Language by @stefanzweifel in https://github.com/spatie/browsershot/pull/701

### New Contributors

- @stefanzweifel made their first contribution in https://github.com/spatie/browsershot/pull/701

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.5...3.57.6

## 3.57.5 - 2022-12-05

### What's Changed

- Add PHP 8.2 Tests Support by @patinthehat in https://github.com/spatie/browsershot/pull/688
- Add Dependabot Automation by @patinthehat in https://github.com/spatie/browsershot/pull/689
- Bump actions/checkout from 2 to 3 by @dependabot in https://github.com/spatie/browsershot/pull/690
- Fixed exception message by @PHLAK in https://github.com/spatie/browsershot/pull/692

### New Contributors

- @patinthehat made their first contribution in https://github.com/spatie/browsershot/pull/688
- @dependabot made their first contribution in https://github.com/spatie/browsershot/pull/690
- @PHLAK made their first contribution in https://github.com/spatie/browsershot/pull/692

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.4...3.57.5

## 3.57.4 - 2022-11-21

### What's Changed

- Allow user to explicitly use a local file for html content. by @daum in https://github.com/spatie/browsershot/pull/687

### New Contributors

- @daum made their first contribution in https://github.com/spatie/browsershot/pull/687

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.3...3.57.4

## 3.57.3 - 2022-10-25

- Do not allow `file://` to be used

## 3.57.2 - 2022-08-19

### What's Changed

- Prevent double request interception on POST by @JeppeKnockaert in https://github.com/spatie/browsershot/pull/664

### New Contributors

- @JeppeKnockaert made their first contribution in https://github.com/spatie/browsershot/pull/664

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.1...3.57.2

## 3.57.1 - 2022-08-03

### What's Changed

- Enable writeOptionsToFile for Windows by @moisish in https://github.com/spatie/browsershot/pull/660

### New Contributors

- @moisish made their first contribution in https://github.com/spatie/browsershot/pull/660

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.57.0...3.57.1

## 3.57.0 - 2022-06-28

### What's Changed

- Set custom temp path by @mtawil in https://github.com/spatie/browsershot/pull/648

### New Contributors

- @mtawil made their first contribution in https://github.com/spatie/browsershot/pull/648

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.56.0...3.57.0

## 3.56.0 - 2022-06-21

- add `failedRequests` method

## 3.55.0 - 2022-06-13

### What's Changed

- Add console messages method by @freekmurze in https://github.com/spatie/browsershot/pull/641

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.54.0...3.55.0

## 3.54.0 - 2022-05-19

## What's Changed

- Added method setContentUrl to set url when using html method by @vitorsemeano in https://github.com/spatie/browsershot/pull/635

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.53.0...3.54.0

## 3.53.0 - 2022-05-09

## What's Changed

- Add support for `omitBackground` by @Ugoku in https://github.com/spatie/browsershot/pull/629
- Ability to set initial page Number for template Headers by @leonelvsc in https://github.com/spatie/browsershot/pull/632

## New Contributors

- @Ugoku made their first contribution in https://github.com/spatie/browsershot/pull/629
- @leonelvsc made their first contribution in https://github.com/spatie/browsershot/pull/632

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.6...3.53.0

## 3.52.6 - 2022-04-06

## What's Changed

- Fixed issue Error: Request is already handled! in NodeJs v17.8.0 by @webaddicto in https://github.com/spatie/browsershot/pull/623

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.5...3.52.6

## 3.52.5 - 2022-04-04

## What's Changed

- Update savePdf to comply with 50eae92 by @marksalmon in https://github.com/spatie/browsershot/pull/622

## New Contributors

- @marksalmon made their first contribution in https://github.com/spatie/browsershot/pull/622

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.4...3.52.5

## 3.52.4 - 2022-04-01

## What's Changed

- Improve exception output when output file is missing
- Use function arguments for `mobile` and `touch` by @orkhanahmadov in https://github.com/spatie/browsershot/pull/610
- Update Puppeteer GitHub link by @ioanschmitt in https://github.com/spatie/browsershot/pull/598
- Add missing 'to' by @ioanschmitt in https://github.com/spatie/browsershot/pull/599
- Fix typo by @noreason in https://github.com/spatie/browsershot/pull/604
- PHPUnit to Pest Converter by @freekmurze in https://github.com/spatie/browsershot/pull/611

## New Contributors

- @ioanschmitt made their first contribution in https://github.com/spatie/browsershot/pull/598
- @noreason made their first contribution in https://github.com/spatie/browsershot/pull/604
- @orkhanahmadov made their first contribution in https://github.com/spatie/browsershot/pull/610

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.3...3.52.4

## 3.52.3 - 2021-12-17

## What's Changed

- Adding compatibility to Symfony 6 by @spackmat in https://github.com/spatie/browsershot/pull/589

## New Contributors

- @spackmat made their first contribution in https://github.com/spatie/browsershot/pull/589

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.2...3.52.3

## 3.52.2 - 2021-12-14

- Add debug output to vague `CouldNotTakeBrowsershot` exception

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.1...3.52.2

## 3.52.1 - 2021-11-24

## What's Changed

- Fix Apple Silicon Path Issue by @aerni in https://github.com/spatie/browsershot/pull/581

## New Contributors

- @aerni made their first contribution in https://github.com/spatie/browsershot/pull/581

**Full Changelog**: https://github.com/spatie/browsershot/compare/3.52.0...3.52.1

## Changelog

All notable changes to `Browsershot` will be documented in this file

## 3.52.0 - 2021-10-27

- Prevent unsuccessful response by @mikaelpopowicz in https://github.com/spatie/browsershot/pull/576

## 3.51.0 - 2021-10-27

- 🚀 Support PHP 8.1 by @Nielsvanpach in https://github.com/spatie/browsershot/pull/567
- Fix incorrect method reference in README by @intrepidws in https://github.com/spatie/browsershot/pull/570

## 3.50.2 - 2021-09-22

- fix `blockDomains` and `blockUrls` methods (#564)

## 3.50.1 - 2021-08-27

- fix browser.js to only abort or continue the request once (#548)

## 3.50.0 - 2021-08-21

- added functionality to only send headers with navigational requests (#542)

## 3.49.0 - 2021-08-05

- add support for the --user-data-dir flag (#540)

## 3.48.0 - 2021-07-28

- support spatie/image v2

## 3.47.0 - 2021-06-10

- re-add support for symfony/process:^4.2

## 3.46.0 - 2021-05-24

- add `base64pdf` method (#512)

## 3.45.0 - 2021-04-20

- add ability to make POST requests (#496)

## 3.44.1. - 2021-04-09

- bump temporary-directory to version 2.0 (#495)

## 3.44.0 - 2021-02-05

- add scale option (#478)

## 3.43.0 - 2021-01-29

- add support for scale option in PDF (#478)

## 3.42.0 - 2021-01-11

- introduce a selectorIndex to bypass querySelector restrictions (#468)

## 3.41.2 - 2020-12-27

- improve local require for puppeteer (#461)

## 3.41.1 - 2020-12-08

- replace `waitFor` with `waitForTimeout` (#452)

## 3.41.0 - 2020-19-11

- adding ability to pass envars to browser instance (#448)

## 3.40.3 - 2020-11-12

- add support for PHP 8

## 3.40.2 - 2020-11-11

- revert changes from previous version

## 3.40.1 - 2020-11-06

- prevent local files from being rendered

## 3.40.0 - 2020-10-07

- added `base64Screenshot`

## 3.39.0 - 2020-09-24

- add `usePipe` to use pipe instead of WebSocket (#423)

## 3.38.0 - 2020-09-22

- pass puppeteer to `callChrome()` (#399)

## 3.37.2 - 2020-07-22

- Replace emulateMedia call with emulateMediaType (#411)

## 3.37.1 - 2020-07-09

- account for the removal of require('puppeteer/DeviceDescriptors') (#406)

## 3.37.0 - 2020-06-17

- get the list of triggered requests (#402)

## 3.36.0 - 2020-04-19

- add use of WS Endpoint option (#390)

## 3.35.0 - 2020-03-03

- adds `blockUrls` and `blockDomains`

## 3.34.10 - 2020-01-04

- adds disableImages method to prevent images from loading (#362)

## 3.33.1 - 2019-11-24

- allow symfony 5 components

## 3.33.0 - 2019-10-17

- allow to connect to remote chromium instance (#341)

## 3.32.1 - 2019-08-02

- fix screenshots not deleting temporary files on the filesystem

## 3.32.0 - 2019-07-19

- add support for HTTP authentication

## 3.31.1 - 2019-07-19

- fix screenshot image manipulations

## 3.31.0 - 2019-06-20

- allow to specify the cookie domain

## 3.30.0 - 2019-06-07

- allow JavaScript to be disabled

## 3.29.0 - 2019-04-24

- add `addChromiumArguments`

## 3.28.0 - 2019-03-29

- add `selectOption`

## 3.27.0 - 2019-03-11

- `paperSize` and `margins` can now use custom units

## 3.26.3 - 2019-02-06

- add `writeOptionsToFile`

## 3.26.2 - 2019-02-01

- fix for setting cookies

## 3.26.1 - 2019-01-10

- update lower deps

## 3.26.0 - 2018-10-18

- new methods `addStyleTag` and `device` added
- fixed a bug where chrome would not shut down properly

## 3.25.1 - 2018-10-08

- improve compatibilty with W-w-windows

## 3.25.0 - 2018-09-02

- add `type`

## 3.24.0 - 2018-08-05

- add `useCookies`

## 3.23.1 - 2018-07-12

- improve compatibility with Windows

## 3.23.0 - 2018-07-12

- added `waitForFunction`

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

### 2.4.2 - 2017-12-24

- update dep on `spatie/image`

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
