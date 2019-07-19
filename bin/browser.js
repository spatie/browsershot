const puppeteer = require('puppeteer');
const fs = require('fs');
const URL = require('url').URL;

const [, , ...args] = process.argv;

/**
 * There are two ways for Browsershot to communicate with puppeteer:
 * - By giving a options JSON dump as an argument
 * - Or by providing a temporary file with the options JSON dump,
 *   the path to this file is then given as an argument with the flag -f
 */
const request = args[0].startsWith('-f ')
    ? JSON.parse(fs.readFileSync(new URL(args[0].substring(3))))
    : JSON.parse(args[0]);

const getOutput = async (page, request) => {
    let output;

    if (request.action == 'evaluate') {
        output = await page.evaluate(request.options.pageFunction);

        return output;
    }

    output = await page[request.action](request.options);

    return output.toString('base64');
};

const callChrome = async () => {
    let browser;
    let page;
    let output;

    try {
        browser = await puppeteer.launch({
            ignoreHTTPSErrors: request.options.ignoreHttpsErrors,
            executablePath: request.options.executablePath,
            args: request.options.args || []
        });

        page = await browser.newPage();

        if (request.options && request.options.disableJavascript) {
            await page.setJavaScriptEnabled(false);
        }

        if (request.options && request.options.dismissDialogs) {
            page.on('dialog', async dialog => {
                await dialog.dismiss();
            });
        }

        if (request.options && request.options.userAgent) {
            await page.setUserAgent(request.options.userAgent);
        }

        if (request.options && request.options.device) {
            const devices = require('puppeteer/DeviceDescriptors');
            const device = devices[request.options.device];
            await page.emulate(device);
        }

        if (request.options && request.options.emulateMedia) {
            await page.emulateMedia(request.options.emulateMedia);
        }

        if (request.options && request.options.viewport) {
            await page.setViewport(request.options.viewport);
        }

        if (request.options && request.options.extraHTTPHeaders) {
            await page.setExtraHTTPHeaders(request.options.extraHTTPHeaders);
        }

        if (request.options && request.options.authentication) {
            await page.authenticate(request.options.authentication);
        }

        if (request.options && request.options.cookies) {
            await page.setCookie(...request.options.cookies);
        }

        if (request.options && request.options.timeout) {
            await page.setDefaultNavigationTimeout(request.options.timeout);
        }

        const requestOptions = {};

        if (request.options && request.options.networkIdleTimeout) {
            requestOptions.waitUntil = 'networkidle';
            requestOptions.networkIdleTimeout = request.options.networkIdleTimeout;
        } else if (request.options && request.options.waitUntil) {
            requestOptions.waitUntil = request.options.waitUntil;
        }

        await page.goto(request.url, requestOptions);

        if (request.options && request.options.types) {
            for (let i = 0, len = request.options.types.length; i < len; i++) {
                let typeOptions = request.options.types[i];
                await page.type(typeOptions.selector, typeOptions.text, {
                    'delay': typeOptions.delay,
                });
            }
        }

        if (request.options && request.options.selects) {
            for (let i = 0, len = request.options.selects.length; i < len; i++) {
                let selectOptions = request.options.selects[i];
                await page.select(selectOptions.selector, selectOptions.value);
            }
        }

        if (request.options && request.options.clicks) {
            for (let i = 0, len = request.options.clicks.length; i < len; i++) {
                let clickOptions = request.options.clicks[i];
                await page.click(clickOptions.selector, {
                    'button': clickOptions.button,
                    'clickCount': clickOptions.clickCount,
                    'delay': clickOptions.delay,
                });
            }
        }

        if (request.options && request.options.addStyleTag) {
            await page.addStyleTag(JSON.parse(request.options.addStyleTag));
        }

        if (request.options.delay) {
            await page.waitFor(request.options.delay);
        }

        if (request.options.selector) {
            const element = await page.$(request.options.selector);
            if (element === null) {
                throw {type: 'ElementNotFound'};
            }

            request.options.clip = await element.boundingBox();
        }

        if (request.options.function) {
            let functionOptions = {
                polling: request.options.functionPolling,
                timeout: request.options.functionTimeout || request.options.timeout
            };
            await page.waitForFunction(request.options.function, functionOptions);
        }

        output = await getOutput(page, request);

        if (!request.options.path) {
            console.log(output);
        }

        await browser.close();
    } catch (exception) {
        if (browser) {
            await browser.close();
        }

        console.error(exception);

        if (exception.type === 'ElementNotFound') {
            process.exit(2);
        }

        process.exit(1);
    }
};

callChrome();
