<h1 align="center">socialweb/atproto-lexicon</h1>

<p align="center">
    <strong>A PHP-based Lexicon parser for applications using the AT Protocol</strong>
</p>

<p align="center">
    <a href="https://github.com/socialweb-php/atproto-lexicon"><img src="https://img.shields.io/badge/source-socialweb/atproto--lexicon-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/socialweb/atproto-lexicon"><img src="https://img.shields.io/packagist/v/socialweb/atproto-lexicon.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/socialweb/atproto-lexicon.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/socialweb-php/atproto-lexicon/blob/main/NOTICE"><img src="https://img.shields.io/packagist/l/socialweb/atproto-lexicon.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/socialweb-php/atproto-lexicon/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/socialweb-php/atproto-lexicon/continuous-integration.yml?branch=main&style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/socialweb-php/atproto-lexicon"><img src="https://img.shields.io/codecov/c/gh/socialweb-php/atproto-lexicon?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/socialweb-php/atproto-lexicon"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Fsocialweb-php%2Fatproto-lexicon%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>

## About

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require socialweb/atproto-lexicon
```

## Usage

``` php
use SocialWeb\Atproto\Lexicon\Parser\LexiconParser;

$parser = new LexiconParser(
    documentPath: '/path/to/bluesky-social/atproto/lexicons/app/bsky/feed/post.json',
    schemaPath: '/path/to/bluesky-social/atproto/lexicons',
    resolveReferences: true,
);

$document = $parser->parse();

// This isn't necessary, but it uses jq to print the JSON and remove null
// values, so you can see that what we parsed is very close to what we consumed.
$json = (string) json_encode($document);
passthru('echo ' . escapeshellarg($json) . ' | jq -C "del(..|nulls)"');
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.

## Copyright and License

socialweb/atproto-lexicon is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the terms of the
GNU Lesser General Public License (LGPL-3.0-or-later) as published by the Free
Software Foundation. Please see [COPYING.LESSER](COPYING.LESSER),
[COPYING](COPYING), and [NOTICE](NOTICE) for more information.
