#!/usr/bin/env node

const puppeteer = require('puppeteer')

const request = JSON.parse(process.argv[2])

let fn = (async () => {
    let browser, page;

    try {
        browser = await puppeteer.launch();

        page = await browser.newPage();

        if (request.options && request.options.userAgent) {
            await page.setUserAgent(request.options.userAgent);
        }

        if (request.options && request.options.viewport) {
            await page.setViewport(request.options.viewport);
        }

        await page.goto(request.url);

        console.log(await page[request.action](request.options));

        await browser.close();
    } catch (e) {
        if (browser) {
            await browser.close();
        }

        console.error(e);

        process.exit(1);
    }
})()
