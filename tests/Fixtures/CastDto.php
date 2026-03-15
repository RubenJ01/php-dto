<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;

final readonly class CastDto
{
    public function __construct(
        #[CastTo('int')]
        public int $age,
        #[CastTo('float')]
        public float $score,
        #[CastTo('bool')]
        public bool $active,
        #[CastTo('string')]
        public string $label,
        #[CastTo('datetime')]
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
