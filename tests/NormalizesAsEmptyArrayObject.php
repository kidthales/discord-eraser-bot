<?php

declare(strict_types=1);

namespace App\Tests;

use ArrayObject;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class NormalizesAsEmptyArrayObject implements NormalizableInterface
{
    /**
     * @param NormalizerInterface $normalizer
     * @param string|null $format
     * @param array $context
     * @return array|string|int|float|bool|ArrayObject|null
     */
    public function normalize(
        NormalizerInterface $normalizer,
        ?string             $format = null,
        array               $context = []
    ): array|string|int|float|bool|ArrayObject|null
    {
        return new ArrayObject();
    }
}
