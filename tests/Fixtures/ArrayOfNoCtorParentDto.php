<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\ArrayOf;

final class ArrayOfNoCtorParentDto
{
    /**
     * @param list<array<string, mixed>> $items
     */
    public function __construct(
        #[ArrayOf(NoConstructorDto::class)]
        public readonly array $items,
    ) {
    }
}
