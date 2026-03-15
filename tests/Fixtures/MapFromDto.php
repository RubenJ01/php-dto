<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;

final readonly class MapFromDto
{
    public function __construct(
        #[MapFrom('first_name')]
        public string $firstName,
        #[MapFrom('last_name')]
        public string $lastName,
        #[MapFrom('stats.play_count')]
        #[CastTo('int')]
        public int $playCount,
    ) {
    }
}
