const puppeteer = require('puppeteer');
const fs = require('fs');
const URL = require('url').URL;
const URLParse = require('url').parse;

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

const requestsList = [];

const getOutput = async (page, request) => {
    let output;

    if (request.action == 'requestsList') {
        output = JSON.stringify(requestsList);

        return output;
    }

    if (request.action == 'evaluate') {
        output = await page.evaluate(request.options.pageFunction);

        return output;
    }

    output = await page[request.action](request.options);

    return output.toString('base64');
};

const callChrome = async pup => {
    let browser;
    let page;
    let output;
    let remoteInstance;
	const puppet = (pup || puppeteer);

    try {
        if (request.options.remoteInstanceUrl || request.options.browserWSEndpoint ) {
            // default options
            let options = {
                ignoreHTTPSErrors: request.options.ignoreHttpsErrors
            };

            // choose only one method to connect to the browser instance
            if ( request.options.remoteInstanceUrl ) {
                options.browserURL = request.options.remoteInstanceUrl;
            } else if ( request.options.browserWSEndpoint ) {
                options.browserWSEndpoint = request.options.browserWSEndpoint;
            }

            try {
                browser = await puppet.connect( options );

                remoteInstance = true;
            } catch (exception) { /** does nothing. fallbacks to launching a chromium instance */}
        }

        if (!browser) {
            browser = await puppet.launch({
                ignoreHTTPSErrors: request.options.ignoreHttpsErrors,
                executablePath: request.options.executablePath,
                args: request.options.args || [],
                pipe: request.options.pipe || false
            });
        }

        page = await browser.newPage();

        if (request.options && request.options.disableJavascript) {
            await page.setJavaScriptEnabled(false);
        }

        await page.setRequestInterception(true);

        page.on('request', request => {
            requestsList.push({
                url: request.url(),
            });
            request.continue();
        });

        if (request.options && request.options.disableImages) {
            page.on('request', request => {
                if (request.resourceType() === 'image')
                    request.abort();
                else
                    request.continue();
            });
        }

        if (request.options && request.options.blockDomains) {
            var domainsArray = JSON.parse(request.options.blockDomains);
            page.on('request', request => {
                const hostname = URLParse(request.url()).hostname;
                domainsArray.forEach(function(value){
                    if (hostname.indexOf(value) >= 0) request.abort();
                });
                request.continue();
            });
        }

        if (request.options && request.options.blockUrls) {
            var urlsArray = JSON.parse(request.options.blockUrls);
            page.on('request', request => {
                urlsArray.forEach(function(value){
                    if (request.url().indexOf(value) >= 0) request.abort();
                });
                request.continue();
            });
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
            const devices = puppeteer.devices;
            const device = devices[request.options.device];
            await page.emulate(device);
        }

        if (request.options && request.options.emulateMedia) {
            await page.emulateMediaType(request.options.emulateMedia);
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

        if (request.options && request.options.disableImages) {
            await page.evaluate(() => {
                let images = document.getElementsByTagName('img');
                while (images.length > 0) {
                    images[0].parentNode.removeChild(images[0]);
                }
            });
        }

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

        if (request.options && request.options.addScriptTag) {
            await page.addScriptTag(JSON.parse(request.options.addScriptTag));
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

        if (remoteInstance && page) {
            await page.close();
        }

        await remoteInstance ? browser.disconnect() : browser.close();
    } catch (exception) {
        if (browser) {

            if (remoteInstance && page) {
                await page.close();
            }

            await remoteInstance ? browser.disconnect() : browser.close();
        }

        console.error(exception);

        if (exception.type === 'ElementNotFound') {
            process.exit(2);
        }

        process.exit(1);
    }
};

if (require.main === module) {
	callChrome();
}

exports.callChrome = callChrome;
