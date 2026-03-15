# PHP DTO

A PHP library to map associative arrays to typed DTOs using attributes.

## Requirements

- PHP >= 8.4

## Installation

```bash
composer require rjds/php-dto
```

## Usage

### Basic mapping

Properties are matched by name — no attributes needed when the array keys already match:

```php
use Rjds\PhpDto\DtoMapper;

final readonly class TagDto
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}
}

$mapper = new DtoMapper();

$tag = $mapper->map([
    'name' => 'rock',
    'url'  => 'https://www.last.fm/tag/rock',
], TagDto::class);

echo $tag->name; // "rock"
```

### `#[MapFrom]` — Rename and nest

Map a property from a differently named key, or use dot-notation for nested paths:

```php
use Rjds\PhpDto\Attribute\MapFrom;

final readonly class UserDto
{
    public function __construct(
        #[MapFrom('first_name')]
        public string $firstName,

        #[MapFrom('registered.unixtime')]
        public string $registeredAt,
    ) {}
}

$user = $mapper->map([
    'first_name' => 'Richard',
    'registered' => ['unixtime' => '1037793040'],
], UserDto::class);

echo $user->firstName;    // "Richard"
echo $user->registeredAt; // "1037793040"
```

### `#[CastTo]` — Type casting

Cast string values (common in JSON APIs) to the correct PHP type:

```php
use Rjds\PhpDto\Attribute\CastTo;

final readonly class StatsDto
{
    public function __construct(
        #[CastTo('int')]
        public int $playcount,

        #[CastTo('bool')]
        public bool $subscriber,

        #[CastTo('datetime')]
        public \DateTimeImmutable $registered,
    ) {}
}

$stats = $mapper->map([
    'playcount'  => '150316',
    'subscriber' => '1',
    'registered' => '1037793040',
], StatsDto::class);

echo $stats->playcount;                // 150316 (int)
echo $stats->subscriber ? 'yes' : 'no'; // "yes"
echo $stats->registered->format('Y');   // "2002"
```

Supported cast types: `int`, `float`, `string`, `bool`, `datetime`.

- `bool` treats `"1"` and `"true"` (case-insensitive) as `true`, everything else as `false`.
- `datetime` treats the value as a unix timestamp and returns a `DateTimeImmutable`.

### `#[ArrayOf]` — Nested DTO collections

Map arrays of associative arrays into arrays of DTOs:

```php
use Rjds\PhpDto\Attribute\ArrayOf;

final readonly class ArtistDto
{
    /** @param list<TagDto> $tags */
    public function __construct(
        public string $name,

        #[ArrayOf(TagDto::class)]
        public array $tags,
    ) {}
}

$artist = $mapper->map([
    'name' => 'Arctic Monkeys',
    'tags' => [
        ['name' => 'rock', 'url' => 'https://www.last.fm/tag/rock'],
        ['name' => 'indie', 'url' => 'https://www.last.fm/tag/indie'],
    ],
], ArtistDto::class);

echo count($artist->tags);    // 2
echo $artist->tags[0]->name;  // "rock"
```

### Default values

Missing keys fall back to the constructor's default value:

```php
final readonly class ProfileDto
{
    public function __construct(
        public string $name,
        public string $country = 'Unknown',
    ) {}
}

$profile = $mapper->map(['name' => 'RJ'], ProfileDto::class);
echo $profile->country; // "Unknown"
```

### Combining attributes

Attributes can be combined freely on a single property:

```php
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;

final readonly class UserDto
{
    public function __construct(
        #[MapFrom('artist_count')]
        #[CastTo('int')]
        public int $artistCount,
    ) {}
}
```

## Compatibility

Tested against the following PHP versions:

- PHP 8.4
- PHP 8.5

## Development

```bash
composer install
php vendor/bin/grumphp run
```

Run mutation testing:

```bash
php vendor/bin/infection --threads=4
```

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

MIT
