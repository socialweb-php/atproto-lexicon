<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type TLexArray from LexArray
 * @phpstan-import-type TLexBlob from LexBlob
 * @phpstan-import-type TLexBoolean from LexBoolean
 * @phpstan-import-type TLexBytes from LexBytes
 * @phpstan-import-type TLexCidLink from LexCidLink
 * @phpstan-import-type TLexInteger from LexInteger
 * @phpstan-import-type TLexObject from LexObject
 * @phpstan-import-type TLexRecord from LexRecord
 * @phpstan-import-type TLexString from LexString
 * @phpstan-import-type TLexToken from LexToken
 * @phpstan-import-type TLexUnknown from LexUnknown
 * @phpstan-import-type TLexXrpcProcedure from LexXrpcProcedure
 * @phpstan-import-type TLexXrpcQuery from LexXrpcQuery
 * @phpstan-import-type TLexXrpcSubscription from LexXrpcSubscription
 * @phpstan-type TLexUserType = TLexArray | TLexBlob | TLexBoolean | TLexBytes | TLexCidLink | TLexInteger | TLexObject | TLexRecord | TLexString | TLexToken | TLexUnknown | TLexXrpcProcedure | TLexXrpcQuery | LexXrpcSubscription
 */
interface LexUserType extends LexEntity
{
}
