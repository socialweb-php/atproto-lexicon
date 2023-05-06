<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexObjectJson from LexObject
 * @phpstan-type LexRecordJson = object{
 *     type: 'record',
 *     description?: string,
 *     key?: string,
 *     record: LexObjectJson,
 * }
 */
final class LexRecord extends LexUserType
{
    public function __construct(
        ?string $description = null,
        public readonly ?string $key = null,
        public readonly ?LexObject $record = null,
    ) {
        parent::__construct(LexUserTypeType::Record, $description);
    }
}
