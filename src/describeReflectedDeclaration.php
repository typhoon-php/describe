<?php

declare(strict_types=1);

namespace Typhoon\Describer;

/**
 * @api
 * @param \ReflectionFunctionAbstract|\ReflectionParameter|\ReflectionClass<object>|\ReflectionProperty $reflection
 * @return non-empty-string
 */
function describeReflectedDeclaration(\ReflectionFunctionAbstract|\ReflectionParameter|\ReflectionClass|\ReflectionProperty $reflection): string
{
    if ($reflection instanceof \ReflectionMethod) {
        return \sprintf('method `%s::%s`', $reflection->class, $reflection->name);
    }

    if ($reflection instanceof \ReflectionFunctionAbstract) {
        if (str_ends_with($reflection->name, '{closure}')) {
            $file = $reflection->getFileName();

            if ($file === false) {
                return 'anonymous function';
            }

            $line = $reflection->getStartLine();

            if ($line === false) {
                return \sprintf('anonymous function at `%s`', $file);
            }

            return \sprintf('anonymous function at `%s:%d`', $file, $line);
        }

        $class = $reflection->getClosureScopeClass();

        if ($class !== null) {
            return \sprintf('method `%s::%s`', $class->name, $reflection->name);
        }

        return \sprintf('function `%s`', $reflection->name);
    }

    if ($reflection instanceof \ReflectionParameter) {
        return \sprintf(
            'parameter #%d `%s` of %s',
            $reflection->getPosition(),
            $reflection->name,
            describeReflectedDeclaration($reflection->getDeclaringFunction()),
        );
    }

    if ($reflection instanceof \ReflectionClass) {
        if ($reflection->isAnonymous()) {
            $file = $reflection->getFileName();

            if ($file === false) {
                return 'anonymous class';
            }

            $line = $reflection->getStartLine();

            if ($line === false) {
                return \sprintf('anonymous class at `%s`', $file);
            }

            return \sprintf('anonymous class at `%s:%d`', $file, $line);
        }

        return \sprintf('class `%s`', $reflection->name);
    }

    return \sprintf(
        'property `%s` of %s',
        $reflection->name,
        describeReflectedDeclaration($reflection->getDeclaringClass()),
    );
}
