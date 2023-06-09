<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Parser;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Parser\DefaultParserFactory;
use SocialWeb\Atproto\Lexicon\Parser\DefaultSchemaRepository;
use SocialWeb\Atproto\Lexicon\Parser\LexXrpcSubscriptionParser;
use SocialWeb\Atproto\Lexicon\Types\LexInteger;
use SocialWeb\Atproto\Lexicon\Types\LexObject;
use SocialWeb\Atproto\Lexicon\Types\LexPrimitive;
use SocialWeb\Atproto\Lexicon\Types\LexString;
use SocialWeb\Atproto\Lexicon\Types\LexType;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcBody;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcError;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcParameters;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscription;
use SocialWeb\Atproto\Lexicon\Types\LexXrpcSubscriptionMessage;

use function json_encode;

class LexXrpcSubscriptionParserTest extends ParserTestCase
{
    public function getParserClassName(): string
    {
        return LexXrpcSubscriptionParser::class;
    }

    /**
     * @param array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[] | LexXrpcParameters | LexXrpcSubscriptionMessage> $checkValues
     */
    #[DataProvider('validValuesProvider')]
    public function testParsesValidValues(object | string $value, array $checkValues): void
    {
        $schemaRepo = new DefaultSchemaRepository(__DIR__ . '/../schemas');

        $parser = new LexXrpcSubscriptionParser();
        $parser->setParserFactory(new DefaultParserFactory($schemaRepo));
        $parsed = $parser->parse($value);

        $this->assertInstanceOf(LexXrpcSubscription::class, $parsed);
        $this->assertSame(LexType::Subscription, $parsed->type);
        $this->assertSame($checkValues['description'] ?? null, $parsed->description);

        // Compare as JSON strings to avoid problems where the LexRef or LexUnion
        // objects in the parsed values fail equality checks due to the parser
        // factory instances they contain in private properties.
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['parameters'] ?? null),
            (string) json_encode($parsed->parameters),
        );
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['message'] ?? null),
            (string) json_encode($parsed->message),
        );
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['infos'] ?? null),
            (string) json_encode($parsed->infos),
        );
        $this->assertJsonStringEqualsJsonString(
            (string) json_encode($checkValues['errors'] ?? null),
            (string) json_encode($parsed->errors),
        );

        if ($parsed->parameters !== null) {
            $this->assertSame($parsed, $parsed->parameters->getParent());
        }

        if ($parsed->message !== null) {
            $this->assertSame($parsed, $parsed->message->getParent());
        }

        foreach ($parsed->errors ?? [] as $error) {
            $this->assertSame($parsed, $error->getParent());
        }

        foreach ($parsed->infos ?? [] as $info) {
            $this->assertSame($parsed, $info->getParent());
        }
    }

    /**
     * @return array<array{value: object | string, checkValues: array<string, scalar | scalar[] | LexXrpcBody | array<string | LexPrimitive> | LexXrpcError[] | LexXrpcParameters | LexXrpcSubscriptionMessage>}>
     */
    public static function validValuesProvider(): array
    {
        return [
            'JSON without properties' => [
                'value' => '{"type":"subscription"}',
                'checkValues' => [],
            ],
            'object without properties' => [
                'value' => (object) ['type' => 'subscription'],
                'checkValues' => [],
            ],
            'JSON with parameters' => [
                'value' => '{"type":"subscription","parameters":{"type":"params","properties":{"foo":{"type":"string"},'
                    . '"bar":{"type":"integer"}}}}',
                'checkValues' => [
                    'parameters' => new LexXrpcParameters(
                        properties: ['foo' => new LexString(), 'bar' => new LexInteger()],
                    ),
                ],
            ],
            'object with parameters' => [
                'value' => (object) [
                    'type' => 'subscription',
                    'parameters' => (object) [
                        'type' => 'params',
                        'properties' => (object) ['baz' => (object) ['type' => 'integer']],
                    ],
                ],
                'checkValues' => ['parameters' => new LexXrpcParameters(properties: ['baz' => new LexInteger()])],
            ],
            'JSON with errors' => [
                'value' => '{"type":"subscription","errors":[{"name":"MyError"},{"name":"YourError"}]}',
                'checkValues' => ['errors' => [new LexXrpcError('MyError'), new LexXrpcError('YourError')]],
            ],
            'object with errors' => [
                'value' => (object) ['type' => 'subscription', 'errors' => [(object) ['name' => 'AnError']]],
                'checkValues' => ['errors' => [new LexXrpcError('AnError')]],
            ],
            'JSON with infos' => [
                'value' => '{"type":"subscription","infos":[{"name":"MyError"},{"name":"YourError"}]}',
                'checkValues' => ['infos' => [new LexXrpcError('MyError'), new LexXrpcError('YourError')]],
            ],
            'object with infos' => [
                'value' => (object) ['type' => 'subscription', 'infos' => [(object) ['name' => 'AnError']]],
                'checkValues' => ['infos' => [new LexXrpcError('AnError')]],
            ],
            'JSON with message' => [
                'value' => '{"type":"subscription","message":{"schema":{"type":"object"}}}',
                'checkValues' => ['message' => new LexXrpcSubscriptionMessage(schema: new LexObject())],
            ],
            'object with message' => [
                'value' => (object) [
                    'type' => 'subscription',
                    'message' => (object) ['schema' => (object) ['type' => 'object']],
                ],
                'checkValues' => ['message' => new LexXrpcSubscriptionMessage(schema: new LexObject())],
            ],
            'JSON with description' => [
                'value' => '{"type":"subscription","description":"Hello there"}',
                'checkValues' => ['description' => 'Hello there'],
            ],
            'object with description' => [
                'value' => (object) ['type' => 'subscription', 'description' => 'Hello there'],
                'checkValues' => ['description' => 'Hello there'],
            ],
        ];
    }

    /**
     * @return array<array{value: object | string}>
     */
    public static function invalidValuesProvider(): array
    {
        return [
            ['value' => ''],
            ['value' => '{}'],
            ['value' => (object) []],
            ['value' => '{"type":"foo"}'],
            ['value' => (object) ['type' => 'foo']],
            ['value' => '{"type":"subscription","parameters":false}'],
            ['value' => (object) ['type' => 'subscription', 'parameters' => 'foobar']],
            ['value' => '{"type":"subscription","errors":123}'],
            ['value' => (object) ['type' => 'subscription', 'errors' => 'foobar']],
            ['value' => '{"type":"subscription","message":"hello"}'],
            ['value' => (object) ['type' => 'subscription', 'message' => false]],
            ['value' => '{"type":"subscription","description":false}'],
            ['value' => (object) ['type' => 'subscription', 'description' => false]],
        ];
    }
}
