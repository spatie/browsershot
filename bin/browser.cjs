const fs = require('fs');
const URL = require('url').URL;
const URLParse = require('url').parse;

if (typeof global.ReadableStream === 'undefined') {
    const {ReadableStream} = require("stream/web");
    global.ReadableStream = ReadableStream;
}

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

const redirectHistory = [];

const consoleMessages = [];

const failedRequests = [];

const pageErrors = [];

const getOutput = async (request, page = null) => {
    let output = {
        requestsList,
        consoleMessages,
        failedRequests,
        redirectHistory,
        pageErrors,
    };

    if (
        ![
            'requestsList',
            'consoleMessages',
            'failedRequests',
            'redirectHistory',
            'pageErrors',
        ].includes(request.action) &&
        page
    ) {
        if (request.action == 'evaluate') {
            output.result = await page.evaluate(request.options.pageFunction);
        } else {
            const result = await page[request.action](request.options);

            // Ignore output result when saving to a file
            output.result = request.options.path
                ? ''
                : (result instanceof Uint8Array ? Buffer.from(result) : result).toString('base64');
        }
    }

    if (page) {
        return JSON.stringify(output);
    }

    // this will allow adding additional error info (only reach this point when there's an exception)
    return output;
};

const callChrome = async pup => {
    let browser;
    let page;
    let remoteInstance;
    const puppet = (pup || require('puppeteer'));
    const options = request.options ?? {};

    const closeBrowser = async () => {
        if (!browser) return;
        if (remoteInstance && page) {
            await page.close();
        }
        await (remoteInstance ? browser.disconnect() : browser.close());
    };

    try {
        if (options.remoteInstanceUrl || options.browserWSEndpoint) {
            // default options
            let connectOptions = {
                acceptInsecureCerts: options.acceptInsecureCerts
            };

            // choose only one method to connect to the browser instance
            if (options.remoteInstanceUrl) {
                connectOptions.browserURL = options.remoteInstanceUrl;
            } else if (options.browserWSEndpoint) {
                connectOptions.browserWSEndpoint = options.browserWSEndpoint;
            }

            try {
                browser = await puppet.connect(connectOptions);

                remoteInstance = true;
            } catch (exception) {

                if (options.throwOnRemoteConnectionError) {
                    console.error(exception.toString());
                    process.exit(4);
                }

                /** fallback to launching a chromium instance */
            }
        }

        if (!browser) {
            browser = await puppet.launch({
                headless: options.newHeadless ? true : 'shell',
                acceptInsecureCerts: options.acceptInsecureCerts,
                executablePath: options.executablePath,
                args: options.args || [],
                pipe: options.pipe || false,
                env: {
                    ...(options.env || {}),
                    ...process.env
                },
                protocolTimeout: options.protocolTimeout ?? 30000,
            });
        }

        page = await browser.newPage();

        if (options.disableJavascript) {
            await page.setJavaScriptEnabled(false);
        }

        const contentUrl = options.contentUrl;
        const parsedContentUrl = contentUrl ? contentUrl.replace(/\/$/, "") : undefined;
        let pageContent;

        if (contentUrl) {
            pageContent = fs.readFileSync(request.url.replace('file://', ''));
            request.url = contentUrl;
        }

        // The request listener captures URLs unconditionally (backing triggeredRequests()) and,
        // when interception is enabled, walks a set of rules that may abort/respond/continue
        // the request. setRequestInterception(true) is only enabled when needed. Unconditional
        // interception routes every request through the CDP, introducing timing anomalies that
        // anti-bot systems can detect.
        const hasItems = (value) => Array.isArray(value) && value.length > 0;
        const hasKeys = (value) => value && typeof value === 'object' && Object.keys(value).length > 0;

        const needsInterception = !!(
            options.disableImages ||
            hasItems(options.blockDomains) ||
            hasItems(options.blockUrls) ||
            options.disableRedirects ||
            hasKeys(options.extraNavigationHTTPHeaders) ||
            pageContent ||
            request.postParams
        );

        const captureUrl = (req) => {
            if (!options.disableCaptureURLS) {
                requestsList.push({ url: req.url() });
            }
        };

        // Each rule below returns true if it terminated the request (via abort/respond/continue).
        // The listener walks them top-to-bottom and stops at the first match.
        const blockImage = (req) => {
            if (!options.disableImages || req.resourceType() !== 'image') return false;
            req.abort();
            return true;
        };

        const blockDomain = (req) => {
            if (!hasItems(options.blockDomains)) return false;
            const hostname = URLParse(req.url()).hostname;
            if (!options.blockDomains.includes(hostname)) return false;
            req.abort();
            return true;
        };

        const blockUrl = (req) => {
            if (!hasItems(options.blockUrls)) return false;
            const matches = options.blockUrls.some(fragment => req.url().indexOf(fragment) >= 0);
            if (!matches) return false;
            req.abort();
            return true;
        };

        const blockRedirect = (req) => {
            if (!options.disableRedirects) return false;
            if (!req.isNavigationRequest() || !req.redirectChain().length) return false;
            req.abort();
            return true;
        };

        const buildHeaders = (req) => {
            const headers = req.headers();
            if (hasKeys(options.extraNavigationHTTPHeaders) && req.isNavigationRequest()) {
                return { ...headers, ...options.extraNavigationHTTPHeaders };
            }
            return headers;
        };

        const respondWithPageContent = (req, headers) => {
            if (!pageContent) return false;
            if (req.url().replace(/\/$/, "") !== parsedContentUrl) return false;
            req.respond({ headers, body: pageContent });
            return true;
        };

        // Note: postParams intentionally uses raw req.headers() rather than the merged
        // `headers`. extraNavigationHTTPHeaders does not apply to postParams (pre-existing).
        const continueWithPostParams = (req) => {
            if (!request.postParams) return false;
            const queryString = Object.entries(request.postParams)
                .map(([key, value]) => `${key}=${value}`)
                .join('&');
            req.continue({
                method: "POST",
                postData: queryString,
                headers: {
                    ...req.headers(),
                    "Content-Type": "application/x-www-form-urlencoded",
                },
            });
            return true;
        };

        if (needsInterception) {
            await page.setRequestInterception(true);
        }

        page.on('request', (req) => {
            captureUrl(req);

            if (!needsInterception) return;

            if (blockImage(req)) return;
            if (blockDomain(req)) return;
            if (blockUrl(req)) return;
            if (blockRedirect(req)) return;

            const headers = buildHeaders(req);

            if (respondWithPageContent(req, headers)) return;
            if (continueWithPostParams(req)) return;

            req.continue({ headers });
        });

        page.on('console', (message) =>
            consoleMessages.push({
                type: message.type(),
                message: message.text(),
                location: message.location(),
                stackTrace: message.stackTrace(),
            })
        );

        page.on('pageerror', (msg) => {
            pageErrors.push({
                name: msg?.name || 'unknown error',
                message: msg?.message || msg?.toString() || 'null'
            });
        });

        page.on('response', function (response) {
            const frame = response.request().frame();
            if (response.request().isNavigationRequest() && frame && frame.parentFrame() === null) {
                redirectHistory.push({
                    url: response.request().url(),
                    status: response.status(),
                    reason: response.statusText(),
                    headers: response.headers()
                })
            }

            if (response.status() >= 200 && response.status() <= 399) {
                return;
            }

            failedRequests.push({
                status: response.status(),
                url: response.url(),
            });
        });

        if (options.dismissDialogs) {
            page.on('dialog', async dialog => {
                await dialog.dismiss();
            });
        }

        if (options.userAgent) {
            await page.setUserAgent(options.userAgent);
        }

        if (options.device) {
            const devices = puppet.KnownDevices;
            const device = devices[options.device];
            await page.emulate(device);
        }

        if (options.emulateMedia) {
            await page.emulateMediaType(options.emulateMedia);
        }

        if (options.emulateMediaFeatures) {
            await page.emulateMediaFeatures(JSON.parse(options.emulateMediaFeatures));
        }

        if (options.viewport) {
            await page.setViewport(options.viewport);
        }

        if (options.extraHTTPHeaders) {
            await page.setExtraHTTPHeaders(options.extraHTTPHeaders);
        }

        if (options.authentication) {
            await page.authenticate(options.authentication);
        }

        if (options.cookies) {
            await page.setCookie(...options.cookies);
        }

        if (options.timeout) {
            await page.setDefaultNavigationTimeout(options.timeout);
        }

        const requestOptions = {};

        if (options.networkIdleTimeout) {
            requestOptions.waitUntil = 'networkidle';
            requestOptions.networkIdleTimeout = options.networkIdleTimeout;
        } else if (options.waitUntil) {
            requestOptions.waitUntil = options.waitUntil;
        }

        const response = await page.goto(request.url, requestOptions);

        if (options.preventUnsuccessfulResponse) {
            const status = response.status()

            if (status >= 400 && status < 600) {
                throw {type: "UnsuccessfulResponse", status};
            }
        }

        if (options.disableImages) {
            await page.evaluate(() => {
                let images = document.getElementsByTagName('img');
                while (images.length > 0) {
                    images[0].parentNode.removeChild(images[0]);
                }
            });
        }

        if (options.types) {
            for (const typeOptions of options.types) {
                await page.type(typeOptions.selector, typeOptions.text, {
                    delay: typeOptions.delay,
                });
            }
        }

        if (options.selects) {
            for (const selectOptions of options.selects) {
                await page.select(selectOptions.selector, selectOptions.value);
            }
        }

        if (options.clicks) {
            for (const clickOptions of options.clicks) {
                await page.click(clickOptions.selector, {
                    button: clickOptions.button,
                    clickCount: clickOptions.clickCount,
                    delay: clickOptions.delay,
                });
            }
        }

        if (options.locatorClicks) {
            for (const clickOptions of options.locatorClicks) {
                try {
                    await page.locator(clickOptions.selector).click({
                        button: clickOptions.button,
                        clickCount: clickOptions.clickCount,
                        delay: clickOptions.delay,
                    });
                } catch (error) {
                    console.error('Timeout error:', error);
                }
            }
        }

        if (options.addStyleTag) {
            await page.addStyleTag(JSON.parse(options.addStyleTag));
        }

        if (options.addScriptTag) {
            await page.addScriptTag(JSON.parse(options.addScriptTag));
        }

        if (options.delay) {
            await new Promise(r => setTimeout(r, options.delay));
        }

        if (options.initialPageNumber) {
            await page.evaluate((initialPageNumber) => {
                window.pageStart = initialPageNumber;

                const style = document.createElement('style');
                style.type = 'text/css';
                style.innerHTML = '.empty-page { page-break-after: always; visibility: hidden; }';
                document.getElementsByTagName('head')[0].appendChild(style);

                const emptyPages = Array.from({length: window.pageStart}).map(() => {
                    const emptyPage = document.createElement('div');
                    emptyPage.className = "empty-page";
                    emptyPage.textContent = "empty";
                    return emptyPage;
                });
                document.body.prepend(...emptyPages);
            }, options.initialPageNumber);
        }

        if (options.function) {
            const functionOptions = {
                polling: options.functionPolling,
                timeout: options.functionTimeout || options.timeout
            };
            await page.waitForFunction(options.function, functionOptions);
        }

        if (options.waitForSelector) {
            await page.waitForSelector(options.waitForSelector, options.waitForSelectorOptions ?? undefined);
        }

        if (options.selector) {
            let element;
            const index = options.selectorIndex || 0;
            if (index) {
                element = await page.$$(options.selector);
                if (!element.length || typeof element[index] === 'undefined') {
                    element = null;
                } else {
                    element = element[index];
                }
            } else {
                element = await page.$(options.selector);
            }
            if (element === null) {
                throw {type: 'ElementNotFound'};
            }

            options.clip = await element.boundingBox();
        }

        console.log(await getOutput(request, page));

        await closeBrowser();
    } catch (exception) {
        await closeBrowser();

        const output = await getOutput(request);

        if (exception.type === 'UnsuccessfulResponse') {
            output.exception = exception.toString();
            console.error(exception.status);
            console.log(JSON.stringify(output));
            process.exit(3);
        }

        output.exception = exception.toString();

        console.error(exception);
        console.log(JSON.stringify(output));

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
