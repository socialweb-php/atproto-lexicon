<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexUnknown;

use function is_string;

/**
 * @phpstan-import-type LexUnknownJson from LexUnknown
 */
class LexUnknownParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexUnknown
    {
        /** @var LexUnknownJson $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexUnknown(
            description: $data->description ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Unknown->value
            && (!isset($data->description) || is_string($data->description));
    }
}
