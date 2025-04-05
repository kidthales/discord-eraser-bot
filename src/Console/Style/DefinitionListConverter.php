<?php

declare(strict_types=1);

namespace App\Console\Style;

use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class DefinitionListConverter
{
    /**
     * @param NormalizerInterface $normalizer
     */
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    /**
     * @param mixed $data
     * @param array $context
     * @return array
     * @throws ExceptionInterface
     */
    public function convert(mixed $data, array $context = []): array
    {
        $normalized = $this->normalizer->normalize($data, null, $context);

        if ($normalized === null || is_scalar($normalized)) {
            return [$normalized];
        }

        $flattened = $this->flatten($normalized);

        $definitionList = [];

        foreach ($flattened as $key => $value) {
            $definitionList[] = [$key => $value];
        }

        return $definitionList;
    }

    /**
     * @param array|ArrayObject $data
     * @param string $keyPrefix
     * @return array
     */
    private function flatten(array|ArrayObject $data, string $keyPrefix = ''): array
    {
        $flattened = [];

        foreach ($data as $key => $value) {
            $flattenedKey = (empty($keyPrefix) ? '' : ($keyPrefix . '.')) . $key;

            if (is_array($value) || $value instanceof ArrayObject) {
                $flattened = [...$flattened, ...$this->flatten($value, $flattenedKey)];
                continue;
            }

            $flattened[$flattenedKey] = $value;
        }

        return $flattened;
    }
}
