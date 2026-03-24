<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;

final class CastDto
{
    public function __construct(
        #[CastTo('int')]
        public readonly int $age,
        #[CastTo('float')]
        public readonly float $score,
        #[CastTo('bool')]
        public readonly bool $active,
        #[CastTo('string')]
        public readonly string $label,
        #[CastTo('datetime')]
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }
}
