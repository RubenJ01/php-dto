<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Attribute;

/**
 * Casts a source value to the specified type.
 *
 * Supported types: 'int', 'float', 'bool', 'string', 'datetime'.
 *
 * - 'bool' treats "1" and "true" (case-insensitive) as true, everything else as false.
 * - 'datetime' converts a unix timestamp (int or numeric string) to DateTimeImmutable.
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class CastTo
{
    public function __construct(
        public string $type,
    ) {
    }
}
