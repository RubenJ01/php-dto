<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

final readonly class DefaultValueDto
{
    public function __construct(
        public string $name,
        public string $country = 'Unknown',
    ) {
    }
}
