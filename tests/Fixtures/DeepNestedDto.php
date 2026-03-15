<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\MapFrom;

final readonly class DeepNestedDto
{
    public function __construct(
        public string $name,
        #[MapFrom('a.b.c')]
        public string $deep = 'fallback',
    ) {
    }
}
