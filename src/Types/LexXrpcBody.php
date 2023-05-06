<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexObjectJson from LexObject
 * @phpstan-import-type LexRefVariantJson from LexRef
 * @phpstan-type LexXrpcBodyJson = object{
 *     description?: string,
 *     encoding: string,
 *     schema?: LexObjectJson | LexRefVariantJson,
 * }
 */
final class LexXrpcBody implements LexEntity
{
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $encoding = null,
        public readonly LexObject | LexRef | LexRefUnion | null $schema = null,
    ) {
    }
}
