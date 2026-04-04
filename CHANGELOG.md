# Changelog

## [Unreleased](https://github.com/RubenJ01/php-dto/compare/v1.1.0...HEAD)

### Features

* add `MappingException` with structured mapping context and optional PHPStan extension for `DtoMapper::map()` return types

### Notes

* Thrown exception types and messages for mapping failures have changed; code that relied on exact `InvalidArgumentException` message text may need updates (catch `MappingException` or match on substrings).

## [1.1.0](https://github.com/RubenJ01/php-dto/compare/v1.0.0...v1.1.0) (2026-03-24)


### Features

* make all code compatible with php 8.1 ([#5](https://github.com/RubenJ01/php-dto/issues/5)) ([7951f7c](https://github.com/RubenJ01/php-dto/commit/7951f7c95b6e5225462fcba7d2c229c8dc51a6b3))

## 1.0.0 (2026-03-15)


### Features

* implement DtoMapper with MapFrom, CastTo, and ArrayOf attributes ([#3](https://github.com/RubenJ01/php-dto/issues/3)) ([6890564](https://github.com/RubenJ01/php-dto/commit/68905645e5918571da92f7ee0d300cb31206aa33))
* initial setup ([ce61c65](https://github.com/RubenJ01/php-dto/commit/ce61c653bc7a22e537595a1abc5a8e0f45535dcf))
