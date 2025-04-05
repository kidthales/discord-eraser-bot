<?php

declare(strict_types=1);

namespace App\Tests;

use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NormalizesAsArrayObject extends NormalizesAsEmptyArrayObject
{
    /**
     * @param string $id
     * @param NormalizesAsArrayObject|null $nested
     */
    public function __construct(public string $id, public ?NormalizesAsArrayObject $nested = null)
    {
    }

    /**
     * @param NormalizerInterface $normalizer
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|ArrayObject|null
     * @throws ExceptionInterface
     */
    public function normalize(
        NormalizerInterface $normalizer,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return new ArrayObject([
            'id' => $this->id,
            'nested' => $normalizer->normalize($this->nested, $format, $context)
        ]);
    }
}
