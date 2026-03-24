<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

final class DefaultValueDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $country = 'Unknown',
    ) {
    }
}
