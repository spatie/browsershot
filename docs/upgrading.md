---
title: Upgrading
weight: 4
---

## Local file access in supplied HTML

Local file requests from HTML passed to `html()` or `setHtml()` are now blocked during rendering. This includes absolute filesystem paths and JavaScript-generated file URLs. PDFs and screenshots that previously loaded local images or stylesheets may now be missing those resources.

For trusted HTML that requires local assets, add `allowLocalFileAccess()` before rendering. This also allows literal `file://` references, which were previously rejected by `html()`. Only opt in for trusted content: Chrome can read any local file its process has permission to access.

`htmlFromFilePath()` retains its existing behavior. See [local file access](/docs/browsershot/v4/usage/introduction#local-file-access) for examples and security considerations.

## From v3 to v4

In v4, the dependency on spatie/image has been removed. If you perform image manipulations, you'll need make sure the spatie/image v3 or higher is required by your project.
