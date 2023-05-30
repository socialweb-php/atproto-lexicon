<?php

declare(strict_types=1);

namespace SocialWeb\Test\Atproto\Lexicon\Validators\Formats;

use PHPUnit\Framework\Attributes\DataProvider;
use SocialWeb\Atproto\Lexicon\Validators\Formats\DatetimeValidator;
use SocialWeb\Atproto\Lexicon\Validators\InvalidValue;
use SocialWeb\Test\Atproto\Lexicon\TestCase;

use function array_map;

class DatetimeValidatorTest extends TestCase
{
    private const NO_ZONE_VALID_ISO_DATE_TIME_STRING = '2019-07-09T15:03:36';

    private const NEGATIVE_TIMEZONES = [
        '12:00', '11:00', '10:00', '09:30', '09:00', '08:00', '07:00', '06:00',
        '05:00', '04:00', '03:30', '03:00', '02:00', '01:00',
    ];

    private const POSITIVE_TIMEZONES = [
        '00:00', '01:00', '02:00', '03:00', '03:30', '04:00', '04:30', '05:00',
        '05:30', '05:45', '06:00', '06:30', '07:00', '08:00', '08:45', '09:00',
        '09:30', '10:00', '10:30', '11:00', '12:00', '12:45', '13:00', '14:00',
    ];

    #[DataProvider('validTestProvider')]
    public function testValidValue(string $value): void
    {
        $this->assertSame($value, (new DatetimeValidator())->validate($value));
    }

    #[DataProvider('invalidTestProvider')]
    public function testInvalidValue(string $value, string $error): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage($error);

        (new DatetimeValidator())->validate($value);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function validTestProvider(): array
    {
        $negativeZoneOffsets = array_map(
            fn (string $v): array => [self::NO_ZONE_VALID_ISO_DATE_TIME_STRING . '-' . $v],
            self::NEGATIVE_TIMEZONES,
        );

        $positiveZoneOffsets = array_map(
            fn (string $v): array => [self::NO_ZONE_VALID_ISO_DATE_TIME_STRING . '+' . $v],
            self::POSITIVE_TIMEZONES,
        );

        return [
            ['2019-07-09T15:03:36.000+00:00'],
            ['2019-07-09T15:03:36Z'],
            ['20190709T150336Z'],
            ['0001-12-31T00:01:15+00:00'],
            ['20220402T025001.957375256+0900'],
            ['2019-07-09T15:03:36'],
            ...$negativeZoneOffsets,
            ...$positiveZoneOffsets,
        ];
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function invalidTestProvider(): array
    {
        return [
            ['2020-12-04', 'Value must be an ISO 8601 formatted datetime string'],
            ['(2023-02-23)', 'Value must be an ISO 8601 formatted datetime string'],
        ];
    }
}
