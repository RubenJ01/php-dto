<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;

/**
 * DTO with mixed-type properties to verify casts produce the correct types.
 */
final class MixedCastDto
{
    public function __construct(
        #[CastTo('int')]
        public readonly mixed $count,
        #[CastTo('float')]
        public readonly mixed $rating,
        #[CastTo('string')]
        public readonly mixed $label,
        #[CastTo('bool')]
        public readonly mixed $active,
    ) {
    }
}
