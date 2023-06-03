<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Nsid\NsidValidator;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexStringFormat;

use function grapheme_strlen;
use function implode;
use function in_array;
use function is_string;
use function strlen;

class LexStringValidator implements Validator
{
    public function __construct(private readonly LexString $type)
    {
    }

    public function validate(mixed $value, ?string $path = null): string
    {
        if ($value === null) {
            $value = $this->type->default;
        }

        $path = $path ?? 'Value';

        if (!is_string($value)) {
            throw new InvalidValue("$path must be a string");
        }

        if ($this->type->const !== null && $value !== $this->type->const) {
            throw new InvalidValue("$path must be {$this->type->const}");
        }

        if ($this->type->enum !== null && !in_array($value, $this->type->enum)) {
            throw new InvalidValue("$path must be one of (" . implode('|', $this->type->enum) . ')');
        }

        if ($this->type->maxLength !== null && strlen($value) > $this->type->maxLength) {
            throw new InvalidValue("$path must not be longer than {$this->type->maxLength} characters");
        }

        if ($this->type->minLength !== null && strlen($value) < $this->type->minLength) {
            throw new InvalidValue("$path must not be shorter than {$this->type->minLength} characters");
        }

        if ($this->type->maxGraphemes !== null && grapheme_strlen($value) > $this->type->maxGraphemes) {
            throw new InvalidValue("$path must not be longer than {$this->type->maxGraphemes} graphemes");
        }

        if ($this->type->minGraphemes !== null && grapheme_strlen($value) < $this->type->minGraphemes) {
            throw new InvalidValue("$path must not be shorter than {$this->type->minGraphemes} graphemes");
        }

        return match ($this->type->format) {
            LexStringFormat::AtIdentifier => (new Formats\AtIdentifierValidator())->validate($value, $path),
            LexStringFormat::AtUri => (new Formats\AtUriValidator())->validate($value, $path),
            LexStringFormat::Cid => (new LexCidLinkValidator())->validate($value, $path),
            LexStringFormat::DateTime => (new Formats\DatetimeValidator())->validate($value, $path),
            LexStringFormat::Did => (new Formats\DidValidator())->validate($value, $path),
            LexStringFormat::Handle => (new Formats\HandleValidator())->validate($value, $path),
            LexStringFormat::Nsid => (new NsidValidator())->validate($value, $path),
            LexStringFormat::Uri => (new Formats\UriValidator())->validate($value, $path),
            default => $value,
        };
    }
}
