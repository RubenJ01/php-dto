<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Rjds\PhpDto\DtoMapper;
use Rjds\PhpDto\Tests\Fixtures\CastDto;
use Rjds\PhpDto\Tests\Fixtures\DeepNestedDto;
use Rjds\PhpDto\Tests\Fixtures\DefaultValueDto;
use Rjds\PhpDto\Tests\Fixtures\MapFromDto;
use Rjds\PhpDto\Tests\Fixtures\MixedCastDto;
use Rjds\PhpDto\Tests\Fixtures\NestedDto;
use Rjds\PhpDto\Tests\Fixtures\NoConstructorDto;
use Rjds\PhpDto\Tests\Fixtures\NullableArrayOfDto;
use Rjds\PhpDto\Tests\Fixtures\SimpleDto;
use Rjds\PhpDto\Tests\Fixtures\TagDto;
use Rjds\PhpDto\Tests\Fixtures\UnsupportedCastDto;

final class DtoMapperTest extends TestCase
{
    private DtoMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DtoMapper();
    }

    // ── Simple mapping (no attributes) ──────────────────────────────────────

    #[Test]
    public function itMapsMatchingKeysByPropertyName(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Arctic Monkeys',
            'url' => 'https://www.last.fm/music/Arctic+Monkeys',
        ], SimpleDto::class);

        $this->assertSame('Arctic Monkeys', $dto->name);
        $this->assertSame('https://www.last.fm/music/Arctic+Monkeys', $dto->url);
    }

    // ── MapFrom attribute ───────────────────────────────────────────────────

    #[Test]
    public function itMapsFromDifferentKeyNames(): void
    {
        $dto = $this->mapper->map([
            'first_name' => 'Richard',
            'last_name' => 'Jones',
            'stats' => ['play_count' => '150316'],
        ], MapFromDto::class);

        $this->assertSame('Richard', $dto->firstName);
        $this->assertSame('Jones', $dto->lastName);
    }

    #[Test]
    public function itMapsFromNestedKeysUsingDotNotation(): void
    {
        $dto = $this->mapper->map([
            'first_name' => 'Richard',
            'last_name' => 'Jones',
            'stats' => ['play_count' => '150316'],
        ], MapFromDto::class);

        $this->assertSame(150316, $dto->playCount);
    }

    #[Test]
    public function itReturnsNullWhenNestedPathHitsNonArrayValue(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Test',
            'a' => ['b' => 'not-an-array'],
        ], DeepNestedDto::class);

        $this->assertSame('fallback', $dto->deep);
    }

    // ── CastTo attribute ────────────────────────────────────────────────────

    #[Test]
    public function itCastsStringToInt(): void
    {
        $dto = $this->mapper->map($this->castData(), CastDto::class);

        $this->assertSame(25, $dto->age);
    }

    #[Test]
    public function itCastsStringToFloat(): void
    {
        $dto = $this->mapper->map($this->castData(), CastDto::class);

        $this->assertSame(9.5, $dto->score);
    }

    #[Test]
    public function itCastsStringOneToBoolTrue(): void
    {
        $dto = $this->mapper->map($this->castData(), CastDto::class);

        $this->assertTrue($dto->active);
    }

    #[Test]
    public function itCastsStringZeroToBoolFalse(): void
    {
        $data = $this->castData();
        $data['active'] = '0';

        $dto = $this->mapper->map($data, CastDto::class);

        $this->assertFalse($dto->active);
    }

    #[Test]
    public function itCastsStringTrueToBoolTrue(): void
    {
        $data = $this->castData();
        $data['active'] = 'true';

        $dto = $this->mapper->map($data, CastDto::class);

        $this->assertTrue($dto->active);
    }

    #[Test]
    public function itCastsUppercaseTrueToBoolTrue(): void
    {
        $data = $this->castData();
        $data['active'] = 'TRUE';

        $dto = $this->mapper->map($data, CastDto::class);

        $this->assertTrue($dto->active);
    }

    #[Test]
    public function itCastsStringFalseToBoolFalse(): void
    {
        $data = $this->castData();
        $data['active'] = 'false';

        $dto = $this->mapper->map($data, CastDto::class);

        $this->assertFalse($dto->active);
    }

    #[Test]
    public function itCastsNonStringTruthyValueToBoolTrue(): void
    {
        $dto = $this->mapper->map([
            'count' => '1',
            'rating' => '1.0',
            'label' => 'x',
            'active' => 1,
        ], MixedCastDto::class);

        $this->assertTrue($dto->active);
    }

    #[Test]
    public function itCastsNonStringFalsyValueToBoolFalse(): void
    {
        $dto = $this->mapper->map([
            'count' => '0',
            'rating' => '0.0',
            'label' => 'x',
            'active' => 0,
        ], MixedCastDto::class);

        $this->assertFalse($dto->active);
    }

    #[Test]
    public function itCastsToString(): void
    {
        $dto = $this->mapper->map($this->castData(), CastDto::class);

        $this->assertSame('42', $dto->label);
    }

    #[Test]
    public function itCastsToDatetime(): void
    {
        $dto = $this->mapper->map($this->castData(), CastDto::class);

        $this->assertSame(1037793040, $dto->createdAt->getTimestamp());
    }

    #[Test]
    public function itActuallyCastsToCorrectTypes(): void
    {
        $dto = $this->mapper->map([
            'count' => '42',
            'rating' => '9.5',
            'label' => 100,
            'active' => '1',
        ], MixedCastDto::class);

        $this->assertIsInt($dto->count);
        $this->assertSame(42, $dto->count);

        $this->assertIsFloat($dto->rating);
        $this->assertSame(9.5, $dto->rating);

        $this->assertIsString($dto->label);
        $this->assertSame('100', $dto->label);

        $this->assertIsBool($dto->active);
        $this->assertTrue($dto->active);
    }

    #[Test]
    public function itThrowsOnUnsupportedCastType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported cast type "xml"');

        $this->mapper->map(['data' => 'test'], UnsupportedCastDto::class);
    }

    // ── ArrayOf attribute ───────────────────────────────────────────────────

    #[Test]
    public function itMapsArrayOfNestedDtos(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Arctic Monkeys',
            'tags' => [
                ['name' => 'rock', 'url' => 'https://www.last.fm/tag/rock'],
                ['name' => 'indie', 'url' => 'https://www.last.fm/tag/indie'],
            ],
        ], NestedDto::class);

        $this->assertCount(2, $dto->tags);
        $this->assertInstanceOf(TagDto::class, $dto->tags[0]);
        $this->assertSame('rock', $dto->tags[0]->name);
        $this->assertSame('indie', $dto->tags[1]->name);
    }

    #[Test]
    public function itMapsEmptyArrayOfDtos(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Unknown Artist',
            'tags' => [],
        ], NestedDto::class);

        $this->assertSame([], $dto->tags);
    }

    #[Test]
    public function itReturnsNullWhenArrayOfValueIsMissing(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Test',
        ], NullableArrayOfDto::class);

        $this->assertNull($dto->tags);
    }

    #[Test]
    public function itPassesThroughNonArrayValueWhenArrayOfIsPresent(): void
    {
        $dto = $this->mapper->map([
            'name' => 'Test',
            'tags' => 'some-string',
        ], NullableArrayOfDto::class);

        $this->assertSame('some-string', $dto->tags);
    }

    // ── Default values ──────────────────────────────────────────────────────

    #[Test]
    public function itUsesDefaultValueWhenKeyIsMissing(): void
    {
        $dto = $this->mapper->map([
            'name' => 'RJ',
        ], DefaultValueDto::class);

        $this->assertSame('RJ', $dto->name);
        $this->assertSame('Unknown', $dto->country);
    }

    #[Test]
    public function itOverridesDefaultValueWhenKeyIsPresent(): void
    {
        $dto = $this->mapper->map([
            'name' => 'RJ',
            'country' => 'United Kingdom',
        ], DefaultValueDto::class);

        $this->assertSame('United Kingdom', $dto->country);
    }

    // ── Error handling ──────────────────────────────────────────────────────

    #[Test]
    public function itThrowsWhenClassHasNoConstructor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must have a constructor');

        $this->mapper->map(['key' => 'value'], NoConstructorDto::class);
    }

    #[Test]
    public function itThrowsWhenArrayOfElementIsNotArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ArrayOf expects each element to be an array');

        $this->mapper->map([
            'name' => 'Test',
            'tags' => ['not-an-array'],
        ], NestedDto::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * @return array<string, mixed>
     */
    private function castData(): array
    {
        return [
            'age' => '25',
            'score' => '9.5',
            'active' => '1',
            'label' => 42,
            'createdAt' => '1037793040',
        ];
    }
}
