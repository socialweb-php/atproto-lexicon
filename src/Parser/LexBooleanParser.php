<?php

declare(strict_types=1);

namespace SocialWeb\Atproto\Lexicon\Parser;

use Closure;
use SocialWeb\Atproto\Lexicon\Types\LexBoolean;
use SocialWeb\Atproto\Lexicon\Types\LexType;

use function is_bool;
use function is_string;

/**
 * @phpstan-import-type TLexBoolean from LexBoolean
 */
class LexBooleanParser implements Parser
{
    use ParserSupport;

    public function parse(object | string $data): LexBoolean
    {
        /** @var TLexBoolean $data */
        $data = $this->validate($data, $this->getValidator());

        return new LexBoolean(
            description: $data->description ?? null,
            default: $data->default ?? null,
            const: $data->const ?? null,
        );
    }

    /**
     * @return Closure(object): bool
     */
    private function getValidator(): Closure
    {
        return fn (object $data): bool => isset($data->type) && $data->type === LexType::Boolean->value
            && (!isset($data->description) || is_string($data->description))
            && (!isset($data->default) || is_bool($data->default))
            && (!isset($data->const) || is_bool($data->const));
    }
}
