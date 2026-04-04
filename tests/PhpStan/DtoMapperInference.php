<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests\PhpStan;

use Rjds\PhpDto\DtoMapper;
use Rjds\PhpDto\Tests\Fixtures\SimpleDto;

/**
 * PHPStan analysis targets: {@see DtoMapper::map()} return type narrowing via phpstan-extension.neon.
 */
final class DtoMapperInference
{
    /**
     * @param array<string, mixed> $data
     */
    public function mapWithClassLiteralYieldsConcreteDto(DtoMapper $mapper, array $data): string
    {
        $dto = $mapper->map($data, SimpleDto::class);

        return $dto->name . $dto->url;
    }
}
