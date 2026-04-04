<?php

declare(strict_types=1);

namespace Rjds\PhpDto\Exception;

/**
 * Thrown when mapping an array to a DTO fails. Use getters for structured context
 * (target class, constructor parameter, resolved map key, array index, optional path segment).
 */
final class MappingException extends \InvalidArgumentException
{
    public function __construct(
        string $message,
        private readonly string $dtoClass,
        private readonly ?string $parameterName = null,
        private readonly ?string $mapKey = null,
        private readonly ?string $pathSegment = null,
        private readonly ?int $arrayIndex = null,
        private readonly ?string $parentDtoClass = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function missingConstructor(string $className): self
    {
        return new self(
            sprintf('Cannot map to %s: a constructor is required.', $className),
            $className,
        );
    }

    public static function unsupportedCast(
        string $dtoClass,
        string $parameterName,
        string $mapKey,
        string $castType,
    ): self {
        return new self(
            sprintf(
                'Unsupported cast type "%s" for parameter $%s (mapped from key "%s") on %s.',
                $castType,
                $parameterName,
                $mapKey,
                $dtoClass,
            ),
            $dtoClass,
            $parameterName,
            $mapKey,
        );
    }

    /**
     * @param non-negative-int $index
     */
    public static function arrayOfItemNotArray(
        string $dtoClass,
        string $parameterName,
        string $mapKey,
        int $index,
        string $actualType,
    ): self {
        return new self(
            sprintf(
                'ArrayOf expects each element to be an array; got %s at index %d '
                . 'for parameter $%s (mapped from key "%s") on %s.',
                $actualType,
                $index,
                $parameterName,
                $mapKey,
                $dtoClass,
            ),
            $dtoClass,
            $parameterName,
            $mapKey,
            null,
            $index,
        );
    }

    /**
     * Wraps a failure raised while mapping an element of an #[ArrayOf] list.
     *
     * @param non-negative-int $index
     */
    public static function nestedWhileMappingArrayOf(
        self $inner,
        string $parentDtoClass,
        string $parentParameterName,
        string $parentMapKey,
        int $index,
    ): self {
        return new self(
            sprintf(
                'Nested mapping failed for parameter $%s (key "%s") at index %d on %s: %s',
                $parentParameterName,
                $parentMapKey,
                $index,
                $parentDtoClass,
                $inner->getMessage(),
            ),
            $inner->getDtoClass(),
            $parentParameterName,
            $parentMapKey,
            null,
            $index,
            $parentDtoClass,
            $inner,
        );
    }

    public function getDtoClass(): string
    {
        return $this->dtoClass;
    }

    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }

    /**
     * Resolved source key, including dot notation when using #[MapFrom].
     */
    public function getMapKey(): ?string
    {
        return $this->mapKey;
    }

    /**
     * When a nested path is invalid or traversal stops early, the segment where resolution ended.
     * Reserved for future use; currently always null.
     */
    public function getPathSegment(): ?string
    {
        return $this->pathSegment;
    }

    /**
     * Zero-based index when the error relates to an #[ArrayOf] list element.
     */
    public function getArrayIndex(): ?int
    {
        return $this->arrayIndex;
    }

    /**
     * When mapping a nested DTO inside #[ArrayOf], the enclosing DTO class name.
     */
    public function getParentDtoClass(): ?string
    {
        return $this->parentDtoClass;
    }

    public function getPreviousMappingException(): ?self
    {
        $previous = $this->getPrevious();

        return $previous instanceof self ? $previous : null;
    }
}
