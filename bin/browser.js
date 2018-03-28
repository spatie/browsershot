const puppeteer = require('puppeteer');

const request = JSON.parse(process.argv[2]);

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

        if (request.options && request.options.dismissDialogs) {
            page.on('dialog', async dialog => {
                await dialog.dismiss();
            });
        }

        if (request.options && request.options.userAgent) {
            await page.setUserAgent(request.options.userAgent);
        }

        if (request.options && request.options.emulateMedia) {
            await page.emulateMedia(request.options.emulateMedia);
        }

        if (request.options && request.options.viewport) {
            await page.setViewport(request.options.viewport);
        }

        const requestOptions = {};

        if (request.options && request.options.networkIdleTimeout) {
            requestOptions.waitUntil = 'networkidle';
            requestOptions.networkIdleTimeout = request.options.networkIdleTimeout;
        } else if (request.options && request.options.waitUntil) {
            requestOptions.waitUntil = request.options.waitUntil;
        }

        await page.goto(request.url, requestOptions);

        if (request.options.delay) {
            await page.waitFor(request.options.delay);
        }

        if (request.options.selector) {
            const element = await page.$(request.options.selector);
            if(element === null) {
                throw { type: 'ElementNotFound' };
            }

            request.options.clip = await element.boundingBox();
        }

        output = await page[request.action](request.options);

        if (!request.options.path) {
            console.log(output.toString('base64'));
        }

        await browser.close();
    } catch (exception) {
        if (browser) {
            await browser.close();
        }

        console.error(exception);

        if(exception.type === 'ElementNotFound') {
            process.exit(2);
        }

        process.exit(1);
    }
};

callChrome();
