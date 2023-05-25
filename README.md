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

socialweb/atproto-lexicon parses [Lexicon schemas][] for the [AT Protocol][].

The current version is compliant with [@atproto/lexicon][] at commit-ish
[85bcd18][].

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require socialweb/atproto-lexicon
```

## Usage

```php
use SocialWeb\Atproto\Lexicon\Nsid\Nsid;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexiconParser;

$schemas = '/path/to/bluesky-social/lexicons';

$schemaRepository = new DefaultSchemaRepository($schemas);
$parser = new LexiconParser(new DefaultParserFactory($schemaRepository));

$nsid = new Nsid('app.bsky.feed.post');
$schemaFile = $schemaRepository->findSchemaPathByNsid($nsid);
$schemaContents = file_get_contents((string) $schemaFile);

$document = $parser->parse((string) $schemaContents);
```

### Resolving References

Using this library, you may resolve references in Lexicon schemas.

For example:

```php
use SocialWeb\Atproto\Lexicon\Types\LexResolvable;

foreach ($document->defs as $defId => $def) {
    if ($def instanceof LexResolvable) {
        $resolved = $def->resolve();
    }
}
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

Copyright © the socialweb/atproto-lexicon Contributors and licensed for use
under the terms of the GNU Lesser General Public License (LGPL-3.0-or-later)
as published by the Free Software Foundation. Please see
[COPYING.LESSER](COPYING.LESSER), [COPYING](COPYING), and [NOTICE](NOTICE)
for more information.


[lexicon schemas]: https://atproto.com/guides/lexicon
[at protocol]: https://atproto.com
[@atproto/lexicon]: https://www.npmjs.com/package/@atproto/lexicon
[85bcd18]: https://github.com/bluesky-social/atproto/blob/85bcd18a7b74908b48e1505737d3b7857772860c/packages/lexicon/src/types.ts
