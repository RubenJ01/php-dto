<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Attribute;

/**
 * Maps a property from a differently named key in the source array.
 *
 * Supports dot-notation for nested paths (e.g. 'registered.unixtime').
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final class MapFrom
{
    public function __construct(
        public readonly string $key,
    ) {
    }
}
