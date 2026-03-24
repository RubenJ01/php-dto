<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\ArrayOf;

final class NullableArrayOfDto
{
    /**
     * @param list<TagDto>|string|null $tags
     */
    public function __construct(
        public readonly string $name,
        #[ArrayOf(TagDto::class)]
        public readonly array|string|null $tags = null,
    ) {
    }
}
