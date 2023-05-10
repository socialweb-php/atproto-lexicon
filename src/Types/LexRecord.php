<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

use JsonSerializable;

/**
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-type TLexRecord = object{
 *     type: 'record',
 *     description?: string,
 *     key?: string,
 *     record: TLexObject,
 * }
 */
class LexRecord implements JsonSerializable, LexUserType
{
    use LexEntityJsonSerializer;
    use LexEntityParent;

    public readonly LexType $type;

    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $key = null,
        public readonly ?LexObject $record = null,
    ) {
        $this->type = LexType::Record;
        $this->record?->setParent($this);
    }
}
