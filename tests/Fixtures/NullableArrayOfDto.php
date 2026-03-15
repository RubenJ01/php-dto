<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\Fixtures;

use Rjds\PhpDto\Attribute\ArrayOf;

final readonly class NullableArrayOfDto
{
    /**
     * @param list<TagDto>|string|null $tags
     */
    public function __construct(
        public string $name,
        #[ArrayOf(TagDto::class)]
        public array|string|null $tags = null,
    ) {
    }
}
