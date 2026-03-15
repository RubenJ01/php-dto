<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;

final readonly class UnsupportedCastDto
{
    public function __construct(
        #[CastTo('xml')]
        public string $data,
    ) {
    }
}
