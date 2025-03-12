<?php

declare(strict_types=1);

namespace Typhoon\Describer;

/**
 * @api
 * @return non-empty-string
 */
function describeValueType(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }

    if ($value === true) {
        return 'true';
    }

    if ($value === false) {
        return 'false';
    }

    if (\is_int($value) || \is_float($value)) {
        return (string) $value;
    }

    if (\is_string($value)) {
        return \sprintf("'%s'", addcslashes($value, "'"));
    }

    if (\is_array($value)) {
        if ($value === []) {
            return 'list{}';
        }

        if (array_is_list($value)) {
            return \sprintf('list{%s}', implode(', ', array_map(describeValueType(...), $value)));
        }

        return \sprintf('array{%s}', implode(', ', array_map(
            static fn(mixed $key, mixed $value): string => \sprintf(
                '%s: %s',
                describeValueType($key),
                describeValueType($value),
            ),
            array_keys($value),
            $value,
        )));
    }

    if ($value instanceof \UnitEnum) {
        return \sprintf('%s::%s', $value::class, $value->name);
    }

    if (\is_object($value)) {
        if (str_contains($value::class, '@')) {
            return 'object';
        }

        return $value::class;
    }

    if (\is_resource($value)) {
        return 'resource';
    }

    /** @var non-empty-string */
    return get_debug_type($value);
}
