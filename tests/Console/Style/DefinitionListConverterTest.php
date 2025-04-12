<?php

declare(strict_types=1);

namespace App\Tests\Console\Style;

use App\Console\Style\DefinitionListConverter;
use App\Tests\NormalizesAsArrayObject;
use App\Tests\NormalizesAsEmptyArrayObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DefinitionListConverterTest extends KernelTestCase
{
    /**
     * @return array
     */
    public static function provider_convert(): array
    {
        return [
            [null, [null]],
            [true, [true]],
            ['test', ['test']],
            [12, [12]],
            [12.7, [12.7]],
            [[], []],
            [['test-key-1' => 'test-value-1'], [['test-key-1' => 'test-value-1']]],
            [
                ['test-key-1' => 'test-value-1', 'test-key-2' => 'test-value-2'],
                [['test-key-1' => 'test-value-1'], ['test-key-2' => 'test-value-2']]
            ],
            [['test-key-1' => [], 'test-key-2' => 'test-value-2'], [['test-key-2' => 'test-value-2']]],
            [
                ['test-key-1' => ['nested-test-key' => 'test-value-1'], 'test-key-2' => 'test-value-2'],
                [['test-key-1.nested-test-key' => 'test-value-1'], ['test-key-2' => 'test-value-2']]
            ],
            [new NormalizesAsEmptyArrayObject(), []],
            [new NormalizesAsArrayObject(id: 'test-id'), [['id' => 'test-id'], ['nested' => null]]],
            [
                new NormalizesAsArrayObject(id: 'test-id', nested: new NormalizesAsArrayObject(id: 'test-id')),
                [['id' => 'test-id'], ['nested.id' => 'test-id'], ['nested.nested' => null]]
            ],
            [
                new NormalizesAsArrayObject(
                    id: 'test-id',
                    nested: new NormalizesAsArrayObject(
                        id: 'test-id',
                        nested: new NormalizesAsArrayObject(id: 'test-id')
                    ),
                ),
                [
                    ['id' => 'test-id'],
                    ['nested.id' => 'test-id'],
                    ['nested.nested.id' => 'test-id'],
                    ['nested.nested.nested' => null]
                ]
            ]
        ];
    }

    /**
     * @return DefinitionListConverter
     */
    private static function getSubject(): DefinitionListConverter
    {
        return new DefinitionListConverter(self::getContainer()->get(NormalizerInterface::class));
    }

    /**
     * @param mixed $subject
     * @param array $expected
     * @return void
     * @dataProvider provider_convert
     * @throws ExceptionInterface
     */
    public function test_convert(mixed $subject, array $expected): void
    {
        self::bootKernel();

        $converter = self::getSubject();

        $actual = $converter->convert($subject);

        self::assertSame(count($expected), count($actual));

        foreach ($expected as $key => $value) {
            self::assertSame($value, $actual[$key]);
        }
    }
}
