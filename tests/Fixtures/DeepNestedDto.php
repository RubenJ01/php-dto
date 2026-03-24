<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\MapFrom;

final class DeepNestedDto
{
    public function __construct(
        public readonly string $name,
        #[MapFrom('a.b.c')]
        public readonly string $deep = 'fallback',
    ) {
    }
}
