<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexRecord;
use SocialWeb\Atproto\Lexicon\Types\LexUserTypeType;

use function is_object;
use function is_string;

final class LexRecordParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexRecord
    {
        /** @var object{type: 'record', record: object, key?: string, description?: string} $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexRecord(
            record: $this->getParserFactory()->getParser(LexObjectParser::class)->parse($data->record),
            key: $data->key ?? null,
            description: $data->description ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexUserTypeType::Record->value
            && isset($data->record) && is_object($data->record)
            && (!isset($data->key) || is_string($data->key))
            && (!isset($data->description) || is_string($data->description));
    }
}
