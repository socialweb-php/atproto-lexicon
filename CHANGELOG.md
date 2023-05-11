# socialweb/atproto-lexicon Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.2.0 - 2023-05-11

### Added

- Add `LexResolvable` interface for classes that can resolve references.
- Apply `LexResolvable` to `LexRef`, `LexRefUnion`, and `LexString`.
- Add `LexCollection` for collections of entities, such as those created when resolving union references or known values for string types.
- Implement resolving of relative references.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Removed `LexString::getLexRefsForKnownValues()`; use `LexString::resolve()` instead.
- Removed `LexRefUnion::getLexRefs()`; use `LexRefUnion::resolve()` instead.

### Fixed

- Nothing.

## 0.1.0 - 2023-05-07

### Added

- Support parsing Lexicon schemas as of commit-ish [aabbf43](https://github.com/bluesky-social/atproto/blob/aabbf43a7f86b37cefbba614d408534b59f59525/packages/lexicon/src/types.ts) of [@atproto/lexicon](https://www.npmjs.com/package/@atproto/lexicon).
  - There is a known issue when attempting to resolve references that use fragment identifiers that assume a reference to a value within the same document (i.e., they use `#something` instead of `com.example.method#something`). This library cannot yet resolve these references.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
