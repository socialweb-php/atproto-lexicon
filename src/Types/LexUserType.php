<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Types;

/**
 * @phpstan-import-type LexArrayJson from LexArray
 * @phpstan-import-type LexBlobJson from LexBlob
 * @phpstan-import-type LexBooleanJson from LexBoolean
 * @phpstan-import-type LexBytesJson from LexBytes
 * @phpstan-import-type LexIntegerJson from LexInteger
 * @phpstan-import-type LexObjectJson from LexObject
 * @phpstan-import-type LexRecordJson from LexRecord
 * @phpstan-import-type LexStringJson from LexString
 * @phpstan-import-type LexTokenJson from LexToken
 * @phpstan-import-type LexUnknownJson from LexUnknown
 * @phpstan-import-type LexXrpcProcedureJson from LexXrpcProcedure
 * @phpstan-import-type LexXrpcQueryJson from LexXrpcQuery
 * @phpstan-type LexUserTypeJson = LexArrayJson | LexBlobJson | LexBooleanJson | LexBytesJson | LexIntegerJson | LexObjectJson | LexRecordJson | LexStringJson | LexTokenJson | LexUnknownJson | LexXrpcProcedureJson | LexXrpcQueryJson
 */
interface LexUserType extends LexEntity
{
}
