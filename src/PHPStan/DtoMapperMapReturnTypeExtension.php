<?php

declare(strict_types=1);

namespace Rjds\PhpDto\PHPStan;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Rjds\PhpDto\DtoMapper;

/**
 * Infers concrete DTO types for {@see DtoMapper::map()} when the class argument is a constant
 * class-string (for example <code>ArtistDto::class</code>) or a generic class-string type.
 */
final class DtoMapperMapReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return DtoMapper::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'map';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope,
    ): ?Type {
        $args = $methodCall->getArgs();
        if (!isset($args[1])) {
            return null;
        }

        $argType = $scope->getType($args[1]->value);

        $constantStrings = $argType->getConstantStrings();
        if ($constantStrings !== []) {
            return new ObjectType($constantStrings[0]->getValue());
        }

        if ($argType->isClassString()->yes()) {
            return $argType->getClassStringObjectType();
        }

        return null;
    }
}
