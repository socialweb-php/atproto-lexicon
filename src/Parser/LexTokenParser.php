<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexToken;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_string;

/**
 * @phpstan-import-type LexTokenJson from LexToken
 */
class LexTokenParser implements Parser
{
    use IsArrayOf;
    use ParserSupport;

    public function parse(object | string $data): LexToken
    {
        /** @var LexTokenJson $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexToken(
            description: $data->description ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Token->value
            && (!isset($data->description) || is_string($data->description));
    }
}
