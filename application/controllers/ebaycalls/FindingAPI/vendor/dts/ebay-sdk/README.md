# EBAY-SDK

[![Build Status](https://travis-ci.org/davidtsadler/ebay-sdk.svg?branch=master)](https://travis-ci.org/davidtsadler/ebay-sdk)

# This is an old version of the SDK.

This repository is now deprecated and will reach its end of life on the 6th December 2015. It is only maintained for bug fixes. A new version of the SDK has been released and is available at [https://github.com/davidtsadler/ebay-sdk-php](https://github.com/davidtsadler/ebay-sdk-php). A guide can be found at [http://devbay.net/sdk/guides/migration/](http://devbay.net/sdk/guides/migration/) to help migrate existing projects to the new repository.

This project enables PHP developers to use the [eBay API](https://go.developer.ebay.com/developers/ebay/documentation-tools/) in their PHP code, and build software using services such as [Finding](http://developer.ebay.com/Devzone/finding/Concepts/FindingAPIGuide.html), [Trading](http://developer.ebay.com/DevZone/guides/ebayfeatures/index.html), [Shopping](http://developer.ebay.com/Devzone/shopping/docs/Concepts/ShoppingAPIGuide.html), etc. You can get started by [installing the SDK via Composer](http://devbay.net/sdk/guides/installation/) and by following the [Getting Started Guide](http://devbay.net/sdk/guides/getting-started/).

## Features

  - Compatible with PHP 5.3.9 or greater.
  - Easy to install with [Composer](http://getcomposer.org/).
  - Compliant with [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

## Resources

  - [User Guides](http://devbay.net/sdk/guides/) - Getting started guide and in-depth information.
  - [SDK Versions](http://devbay.net/sdk/guides/versions/) - A complete list of each SDK, and the API version they support.
  - [Sample Project](https://github.com/davidtsadler/ebay-sdk-examples) - Provides several examples of using the SDK.
  - [Google Group](https://groups.google.com/forum/#!forum/ebay-sdk-php) - Join for support with the SDK.
  - [@devbaydotnet](https://twitter.com/devbaydotnet) - Follow on Twitter for announcements of releases, important changes and so on.

## Requirements

  - PHP 5.3.3 or greater with the following extensions:
      - cURL
      - libxml
  - 64 bit version of PHP recommended as there are some [issues when using the SDK with 32 bit](http://devbay.net/sdk/guides/requirements/#issues).
  - SSL enabled on the cURL extension so that https requests can be made.

## SDK Repositories

Due to the vast size of the eBay API, this project is just the main dependency for several projects that are shown below. Each of these projects provide a SDK for interfacing with one of the eBay services.

  - [Finding](https://github.com/davidtsadler/ebay-sdk-finding)
  - [Trading](https://github.com/davidtsadler/ebay-sdk-trading)
  - [Shopping](https://github.com/davidtsadler/ebay-sdk-shopping)
  - [Business Policies Management](https://github.com/davidtsadler/ebay-sdk-business-policies-management)

## Project Goals

  - Be well maintained.
  - Be [well documented](http://devbay.net/sdk/guides/).
  - Be [well tested](https://github.com/davidtsadler/ebay-sdk/tree/master/test/DTS/eBaySDK).
  - Be well supported with [working examples](https://github.com/davidtsadler/ebay-sdk-examples).

## Project Maturity

This is a personal project that has been developed by me, [David T. Sadler](http://twitter.com/davidtsadler). I decided to create this project to make up for the lack of an official SDK for PHP. It is in no way endorsed, sponsored or maintained by eBay.

As this is a brand new project you should expect frequent releases until it reaches the stable `1.0.0` target. I will endeavour to keep changes to a minimum between each release and any changes will be [documented](https://github.com/davidtsadler/ebay-sdk/blob/master/CHANGELOG.md).

## License

Licensed under the [Apache Public License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html).

Copyright 2014 [David T. Sadler](http://twitter.com/davidtsadler)
