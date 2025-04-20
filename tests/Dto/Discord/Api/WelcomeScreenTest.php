<?php

declare(strict_types=1);

namespace App\Tests\Dto\Discord\Api;

use App\Dto\Discord\Api\WelcomeScreen;
use App\Tests\SubjectSerializable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class WelcomeScreenTest extends KernelTestCase
{
    use SubjectSerializable;

    /**
     * @param WelcomeScreen $expected
     * @param WelcomeScreen $actual
     * @return void
     */
    public static function assertDeepSame(mixed $expected, mixed $actual): void
    {
        self::assertSame($expected->description, $actual->description);

        self::assertSame(count($expected->welcome_channels), count($actual->welcome_channels));

        for ($i = 0; $i < count($expected->welcome_channels); ++$i) {
            WelcomeScreenChannelTest::assertDeepSame($expected->welcome_channels[$i], $actual->welcome_channels[$i]);
        }
    }

    /**
     * @return array
     */
    public static function provider_deserialization(): array
    {
        $subjectTemplate = '{"description":%s,"welcome_channels":[%s]}';

        $welcomeChannelTemplates = [];
        $welcomeChannelsExpected = [];

        foreach (WelcomeScreenChannelTest::provider_deserialization() as [$welcomeChannelTemplate, $welcomeChannelExpected]) {
            $welcomeChannelTemplates[] = $welcomeChannelTemplate;
            $welcomeChannelsExpected[] = $welcomeChannelExpected;
        }

        return [
            [sprintf($subjectTemplate, 'null', ''), new WelcomeScreen(description: null, welcome_channels: [])],
            [
                sprintf($subjectTemplate, '"test-description"', implode(',', $welcomeChannelTemplates)),
                new WelcomeScreen(description: 'test-description', welcome_channels: $welcomeChannelsExpected)
            ]
        ];
    }

    /**
     * @param string $subject
     * @param WelcomeScreen $expected
     * @return void
     * @dataProvider provider_deserialization
     */
    public function test_deserialization(string $subject, WelcomeScreen $expected): void
    {
        self::bootKernel();
        self::testDeserialization($subject, $expected, WelcomeScreen::class, 'json');
    }
}
