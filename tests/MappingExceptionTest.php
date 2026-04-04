<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\Exception\MappingException;
use Rjds\PhpDto\Tests\Fixtures\ArrayOfNoCtorParentDto;
use Rjds\PhpDto\Tests\Fixtures\NoConstructorDto;
use Rjds\PhpDto\Tests\Fixtures\SimpleDto;

final class MappingExceptionTest extends TestCase
{
    #[Test]
    public function itReturnsNullFromGetPreviousMappingExceptionWhenPreviousIsNotMappingException(): void
    {
        $e = new MappingException(
            'wrapped',
            SimpleDto::class,
            null,
            null,
            null,
            null,
            null,
            new \RuntimeException('underlying'),
        );

        $this->assertNull($e->getPreviousMappingException());
    }

    #[Test]
    public function itReturnsInnerFromGetPreviousMappingExceptionWhenNested(): void
    {
        $inner = MappingException::missingConstructor(NoConstructorDto::class);
        $outer = MappingException::nestedWhileMappingArrayOf(
            $inner,
            ArrayOfNoCtorParentDto::class,
            'items',
            'items',
            0,
        );

        $this->assertSame($inner, $outer->getPreviousMappingException());
    }

    #[Test]
    public function gettersReturnExpectedValuesForUnsupportedCast(): void
    {
        $e = MappingException::unsupportedCast(SimpleDto::class, 'name', 'name', 'xml');

        $this->assertSame(SimpleDto::class, $e->getDtoClass());
        $this->assertSame('name', $e->getParameterName());
        $this->assertSame('name', $e->getMapKey());
        $this->assertNull($e->getPathSegment());
        $this->assertNull($e->getArrayIndex());
        $this->assertNull($e->getParentDtoClass());
        $this->assertSame(0, $e->getCode());
    }

    #[Test]
    public function missingConstructorHasExceptionCodeZero(): void
    {
        $e = MappingException::missingConstructor(NoConstructorDto::class);

        $this->assertSame(0, $e->getCode());
    }
}
