<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-type TLexBoolean = object{
 *     type: 'boolean',
 *     description?: string,
 *     default?: bool,
 *     const?: bool,
 * }
 */
class LexBoolean implements JsonSerializable, LexPrimitive, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?bool $default = null,
        public readonly ?bool $const = null,
    ) {
        $this->type = LexType::Boolean;
    }
}
