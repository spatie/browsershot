---
title: Requirements
weight: 3
---

This package requires node 7.6.0 or higher and the Puppeteer Node library.

### Installing puppeteer on MacOS

On MacOS you can install Puppeteer in your project via NPM:

```bash
npm install puppeteer
```

Or you could opt to just install it globally

```bash
npm install puppeteer --global
```

### Installing puppeteer a Forge provisioned server

On a [Forge](https://forge.laravel.com) provisioned Ubuntu 20.04 server you can install the latest stable version of Chrome like this:

```bash
curl -sL https://deb.nodesource.com/setup_14.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev libxshmfence-dev
sudo npm install --global --unsafe-perm puppeteer
sudo chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium
```

### Custom node and npm binaries

Depending on your setup, node or npm might be not directly available to Browsershot.
If you need to manually set these binary paths, you can do this by calling the `setNodeBinary` and `setNpmBinary` method.

```
Browsershot::html('Foo')
    ->setNodeBinary('/usr/local/bin/node')
    ->setNpmBinary('/usr/local/bin/npm');
```

By default, Browsershot will use `node` and `npm` to execute commands.

### Custom include path

If you don't want to manually specify binary paths, but rather modify the include path in general,
you can set it using the `setIncludePath` method.

```php
Browsershot::html('Foo')
    ->setIncludePath('$PATH:/usr/local/bin')
```

Setting the include path can be useful in cases where `node` and `npm` can not be found automatically.

### Custom node module path

If you want to use an alternative `node_modules` source you can set it using the `setNodeModulePath` method.

```php
Browsershot::html('Foo')
  ->setNodeModulePath("/path/to/my/project/node_modules/")
```

### Custom binary path

If you want to use an alternative script source you can set it using the `setBinPath` method.

```php
Browsershot::html('Foo')
  ->setBinPath("/path/to/my/project/my_script.js")
```

### Custom chrome/chromium executable path

If you want to use an alternative chrome or chromium executable from what is installed by puppeteer you can set it using the `setChromePath` method.

```php
Browsershot::html('Foo')
  ->setChromePath("/path/to/my/chrome")
```

### Pass custom arguments to Chromium

If you need to pass custom arguments to Chromium, use the `addChromiumArguments` method.

The method accepts an `array` of key/value pairs, or simply values. All of these arguments will automatically be prefixed with `--`.

```php
Browsershot::html('Foo')
  ->addChromiumArguments([
      'some-argument-without-a-value',
      'keyed-argument' => 'argument-value',
  ]);
```

If no key is provided, then the argument is passed through as-is.

| Example array | Flags that will be passed to Chromium |
| - | - |
| `['foo']` | `--foo` |
| `['foo', 'bar']` | `--foo --bar` |
| `['foo', 'bar' => 'baz' ]` | `--foo --bar=baz` |

This method can be useful in order to pass a flag to fix font rendering issues on some Linux distributions (e.g. CentOS).

```php
Browsershot::html('Foo')
  ->addChromiumArguments([
      'font-render-hinting' => 'none',
  ]);
```

