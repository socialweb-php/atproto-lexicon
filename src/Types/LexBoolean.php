<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-type LexBooleanJson = object{
 *     type: 'boolean',
 *     description?: string,
 *     default?: bool,
 *     const?: bool,
 * }
 */
final class LexBoolean extends LexPrimitive
{
    public function __construct(
        ?string $description = null,
        public readonly ?bool $default = null,
        public readonly ?bool $const = null,
    ) {
        parent::__construct(LexPrimitiveType::Boolean, $description);
    }
}
