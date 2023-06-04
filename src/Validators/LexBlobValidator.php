<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Validators;

use SocialWeb\Atproto\Lexicon\Types\LexBlob;
use Traversable;

use function array_keys;
use function get_object_vars;
use function in_array;
use function is_float;
use function is_int;
use function is_object;
use function is_string;
use function ksort;
use function preg_match;
use function strtolower;

class LexBlobValidator implements Validator
{
    use DictionaryValidation;

    private const MIME_TYPE_PATTERN = '/^.+\/.+$/';
    private const TYPED_JSON_BLOB_REF_KEYS = ['$type', 'mimetype', 'ref', 'size'];
    private const UNTYPED_JSON_BLOB_REF_KEYS = ['cid', 'mimetype'];

    public function __construct(private readonly LexBlob $type)
    {
    }

    /**
     * @return iterable<string, mixed> | object
     */
    public function validate(mixed $value, ?string $path = null): iterable | object
    {
        $path = $path ?? 'Value';

        if (!$this->isDictionary($value)) {
            throw new InvalidValue("$path must be a blob ref");
        }

        $blobRef = $this->normalizeBlobRef($value);
        if (!$this->isTypedJsonBlobRef($blobRef) && !$this->isUntypedJsonBlobRef($blobRef)) {
            throw new InvalidValue("$path must be a blob ref");
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $value
     */
    private function isTypedJsonBlobRef(array $value): bool
    {
        if (array_keys($value) !== self::TYPED_JSON_BLOB_REF_KEYS) {
            return false;
        }

        if ($value['$type'] !== 'blob') {
            return false;
        }

        if (!$this->isDictionary($value['ref'])) {
            return false;
        }

        if (!$this->isValidSize($value['size'])) {
            return false;
        }

        return $this->isValidMimeType($value['mimetype']);
    }

    /**
     * @param array<string, mixed> $value
     */
    private function isUntypedJsonBlobRef(array $value): bool
    {
        if (array_keys($value) !== self::UNTYPED_JSON_BLOB_REF_KEYS) {
            return false;
        }

        if (!is_string($value['cid'])) {
            return false;
        }

        return $this->isValidMimeType($value['mimetype']);
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-param iterable<string, mixed> | object $value
     */
    private function normalizeBlobRef(iterable | object $value): array
    {
        $blobRef = [];

        if (is_object($value) && !$value instanceof Traversable) {
            $value = get_object_vars($value);
        }

        /**
         * @var string $k
         * @var mixed $v
         */
        foreach ($value as $k => $v) {
            /** @psalm-suppress MixedAssignment */
            $blobRef[strtolower($k)] = $v;
        }

        ksort($blobRef);

        return $blobRef;
    }

    private function isValidSize(mixed $size): bool
    {
        if (!is_int($size) && !is_float($size)) {
            return false;
        }

        return $this->type->maxSize === null || $size <= $this->type->maxSize;
    }

    private function isValidMimeType(mixed $mimeType): bool
    {
        if (!is_string($mimeType)) {
            return false;
        }

        if (!preg_match(self::MIME_TYPE_PATTERN, $mimeType)) {
            return false;
        }

        return $this->type->accept === null || in_array($mimeType, $this->type->accept);
    }
}
