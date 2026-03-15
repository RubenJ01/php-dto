<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Attribute;

/**
 * Indicates that the property is an array where each element should be mapped to the given DTO class.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class ArrayOf
{
    /**
     * @param class-string $className
     */
    public function __construct(
        public string $className,
    ) {
    }
}
