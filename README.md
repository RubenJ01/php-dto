# PHP DTO

![Version](https://img.shields.io/github/v/release/RubenJ01/php-dto?label=version)
[![Packagist Downloads](https://img.shields.io/packagist/dt/rjds/php-dto)](https://packagist.org/packages/rjds/php-dto)
[![codecov](https://codecov.io/github/RubenJ01/php-dto/graph/badge.svg)](https://codecov.io/github/RubenJ01/php-dto)
![License](https://img.shields.io/github/license/RubenJ01/php-dto)

A PHP library to map associative arrays to typed DTOs using attributes.

## Installation

Install from Packagist:

```bash
composer require rjds/php-dto
```

Requirements:

- PHP 8.1 or higher

## Overview

`DtoMapper` converts associative arrays to typed constructor-based DTOs.

```php
use Rjds\PhpDto\Attribute\ArrayOf;
use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;
use Rjds\PhpDto\DtoMapper;

final class TagDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
    ) {
    }
}

final class ArtistDto
{
    /** @param list<TagDto> $tags */
    public function __construct(
        public readonly string $name,
        #[MapFrom('stats.play_count')]
        #[CastTo('int')]
        public readonly int $playCount,
        #[ArrayOf(TagDto::class)]
        public readonly array $tags,
    ) {
    }
}

$mapper = new DtoMapper();

$artist = $mapper->map([
    'name' => 'Arctic Monkeys',
    'stats' => ['play_count' => '150316'],
    'tags' => [
        ['name' => 'rock', 'url' => 'https://www.last.fm/tag/rock'],
        ['name' => 'indie', 'url' => 'https://www.last.fm/tag/indie'],
    ],
], ArtistDto::class);

echo $artist->playCount;   // 150316 (int)
echo $artist->tags[0]->name; // rock
```

## Features

- Zero-boilerplate name-based mapping for constructor arguments
- `#[MapFrom]` support for renamed and nested keys using dot notation
- `#[CastTo]` support for `int`, `float`, `string`, `bool`, and `datetime`
- `#[ArrayOf]` support for mapping nested DTO collections
- Constructor default values respected when source keys are missing

## Quick Reference

### `#[MapFrom]`

Map a parameter from a different key, including nested paths:

```php
#[MapFrom('profile.first_name')]
public readonly string $firstName
```

### `#[CastTo]`

Cast scalar string input into strongly typed DTO fields:

```php
#[CastTo('datetime')]
public readonly \DateTimeImmutable $registeredAt
```

### `#[ArrayOf]`

Map list entries to nested DTO instances:

```php
#[ArrayOf(TagDto::class)]
public readonly array $tags
```

## Packagist

- Package: [`rjds/php-dto`](https://packagist.org/packages/rjds/php-dto)
- Source: <https://github.com/RubenJ01/php-dto>
- Issues: <https://github.com/RubenJ01/php-dto/issues>

The repository includes `name`, `license`, `keywords`, `homepage`, and `support` metadata in `composer.json` for Packagist indexing.

## Development

```bash
composer install
php vendor/bin/grumphp run
```

Run mutation testing:

```bash
php vendor/bin/infection --threads=4
```

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) for branch strategy, commit conventions, and PR workflow.

## License

This project is released under the MIT License. See [LICENSE](LICENSE) for details and [CHANGELOG.md](CHANGELOG.md) for release history.
