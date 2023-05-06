<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexStringFormatJson from LexStringFormat
 * @phpstan-type LexStringJson = object{
 *     type: 'string',
 *     format?: LexStringFormatJson,
 *     description?: string,
 *     default?: string,
 *     minLength?: int,
 *     maxLength?: int,
 *     minGraphemes?: int,
 *     maxGraphemes?: int,
 *     enum?: string[],
 *     const?: string,
 *     knownValues?: string[],
 * }
 */
final class LexString extends LexPrimitive
{
    /**
     * @param string[] | null $enum
     * @param string[] | null $knownValues
     */
    public function __construct(
        public readonly ?LexStringFormat $format = null,
        ?string $description = null,
        public readonly ?string $default = null,
        public readonly ?int $minLength = null,
        public readonly ?int $maxLength = null,
        public readonly ?int $minGraphemes = null,
        public readonly ?int $maxGraphemes = null,
        public readonly ?array $enum = null,
        public readonly ?string $const = null,
        public readonly ?array $knownValues = null,
    ) {
        parent::__construct(LexPrimitiveType::String, $description);
    }
}
