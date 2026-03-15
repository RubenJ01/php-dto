<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\CastTo;

/**
 * DTO with mixed-type properties to verify casts produce the correct types.
 */
final readonly class MixedCastDto
{
    public function __construct(
        #[CastTo('int')]
        public mixed $count,
        #[CastTo('float')]
        public mixed $rating,
        #[CastTo('string')]
        public mixed $label,
        #[CastTo('bool')]
        public mixed $active,
    ) {
    }
}
