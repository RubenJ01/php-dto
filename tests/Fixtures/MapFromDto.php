<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;

final class MapFromDto
{
    public function __construct(
        #[MapFrom('first_name')]
        public readonly string $firstName,
        #[MapFrom('last_name')]
        public readonly string $lastName,
        #[MapFrom('stats.play_count')]
        #[CastTo('int')]
        public readonly int $playCount,
    ) {
    }
}
