# Changelog

## [2.0.0](https://github.com/RubenJ01/php-dto/compare/v1.1.0...v2.0.0) (2026-04-18)

### ⚠ BREAKING CHANGES

* Mapping failures now throw `Rjds\PhpDto\Exception\MappingException` instead of bare `InvalidArgumentException`. `MappingException` extends `InvalidArgumentException`, so `catch (InvalidArgumentException)` continues to work; strict class checks such as `get_class($e) === InvalidArgumentException::class` may need updating.

### Features

* Add `MappingException` with structured context (DTO class, parameter, map key, array index, nested `ArrayOf` parent)
* Optional PHPStan extension (`phpstan-extension.neon`) for inferring concrete DTO types from `DtoMapper::map()`
* Improve `DtoMapper` mapping error behaviour and nested `ArrayOf` error chaining

## [1.1.0](https://github.com/RubenJ01/php-dto/compare/v1.0.0...v1.1.0) (2026-03-24)


### Features

* make all code compatible with php 8.1 ([#5](https://github.com/RubenJ01/php-dto/issues/5)) ([7951f7c](https://github.com/RubenJ01/php-dto/commit/7951f7c95b6e5225462fcba7d2c229c8dc51a6b3))

## 1.0.0 (2026-03-15)


### Features

* implement DtoMapper with MapFrom, CastTo, and ArrayOf attributes ([#3](https://github.com/RubenJ01/php-dto/issues/3)) ([6890564](https://github.com/RubenJ01/php-dto/commit/68905645e5918571da92f7ee0d300cb31206aa33))
* initial setup ([ce61c65](https://github.com/RubenJ01/php-dto/commit/ce61c653bc7a22e537595a1abc5a8e0f45535dcf))
