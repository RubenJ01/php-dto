<?php

declare(strict_types=1);

namespace Rjds\PhpDto;

use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpDto\Exception\MappingException;

final class DtoMapper
{
    /**
     * Map an associative array to a DTO instance.
     *
     * @template T of object
     * @param array<string, mixed> $data The source data
     * @param class-string<T> $className The target DTO class
     * @return T
     */
    public function map(array $data, string $className): object
    {
        return $this->mapInternal($data, $className);
    }

    /**
     * @template T of object
     * @param array<string, mixed> $data
     * @param class-string<T> $className
     * @return T
     */
    private function mapInternal(array $data, string $className): object
    {
        $reflection = new \ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            throw MappingException::missingConstructor($className);
        }

        $args = [];

        foreach ($constructor->getParameters() as $parameter) {
            $value = $this->resolveValue($parameter, $data);
            $args[] = $value;
        }

        return $reflection->newInstanceArgs($args);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveValue(\ReflectionParameter $parameter, array $data): mixed
    {
        $key = $this->resolveKey($parameter);
        $value = $this->extractValue($data, $key);

        if ($value === null && $parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $value = $this->applyCast($parameter, $value, $key);
        $value = $this->applyArrayOf($parameter, $value);

        return $value;
    }

    private function resolveKey(\ReflectionParameter $parameter): string
    {
        $attributes = $parameter->getAttributes(MapFrom::class);

        if ($attributes !== []) {
            /** @var MapFrom $mapFrom */
            $mapFrom = $attributes[0]->newInstance();

            return $mapFrom->key;
        }

        return $parameter->getName();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractValue(array $data, string $key): mixed
    {
        $segments = explode('.', $key);
        $current = $data;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return null;
            }
            /** @var mixed $current */
            $current = $current[$segment];
        }

        return $current;
    }

    private function applyCast(\ReflectionParameter $parameter, mixed $value, string $mapKey): mixed
    {
        $attributes = $parameter->getAttributes(CastTo::class);

        if ($attributes === []) {
            return $value;
        }

        /** @var CastTo $castTo */
        $castTo = $attributes[0]->newInstance();

        $dtoClass = $parameter->getDeclaringClass()?->getName() ?? 'unknown';

        return match ($castTo->type) {
            'int' => (int) $value, // @phpstan-ignore cast.int
            'float' => (float) $value, // @phpstan-ignore cast.double
            'string' => (string) $value, // @phpstan-ignore cast.string
            'bool' => is_string($value)
                ? in_array(strtolower($value), ['1', 'true'], true)
                : (bool) $value,
            'datetime' => (new \DateTimeImmutable())->setTimestamp((int) $value), // @phpstan-ignore cast.int
            default => throw MappingException::unsupportedCast(
                $dtoClass,
                $parameter->getName(),
                $mapKey,
                $castTo->type,
            ),
        };
    }

    private function applyArrayOf(\ReflectionParameter $parameter, mixed $value): mixed
    {
        $attributes = $parameter->getAttributes(ArrayOf::class);

        if ($attributes === [] || !is_array($value)) {
            return $value;
        }

        /** @var ArrayOf $arrayOf */
        $arrayOf = $attributes[0]->newInstance();

        $declaringClass = $parameter->getDeclaringClass();
        $parentDtoClass = $declaringClass !== null ? $declaringClass->getName() : $parameter->getName();
        $mapKey = $this->resolveKey($parameter);

        $items = [];
        /** @var mixed $item */
        foreach (array_values($value) as $index => $item) {
            if (!is_array($item)) {
                throw MappingException::arrayOfItemNotArray(
                    $parentDtoClass,
                    $parameter->getName(),
                    $mapKey,
                    $index,
                    get_debug_type($item),
                );
            }
            /** @var array<string, mixed> $item */
            try {
                $items[] = $this->mapInternal($item, $arrayOf->className);
            } catch (MappingException $e) {
                throw MappingException::nestedWhileMappingArrayOf(
                    $e,
                    $parentDtoClass,
                    $parameter->getName(),
                    $mapKey,
                    $index,
                );
            }
        }

        return $items;
    }
}
