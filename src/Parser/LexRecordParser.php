<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_object;
use function is_string;

/**
 * @phpstan-import-type LexRecordJson from LexRecord
 */
final class LexRecordParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexRecord
    {
        /** @var LexRecordJson $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexRecord(
            description: $data->description ?? null,
            key: $data->key ?? null,
            record: $this->getParserFactory()->getParser(LexObjectParser::class)->parse($data->record),
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Record->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->key) || is_string($data->key))
            && isset($data->record) && is_object($data->record);
    }
}
