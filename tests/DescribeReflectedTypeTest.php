<?php

declare(strict_types=1);

namespace Typhoon\Describer;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversFunction('Typhoon\Describer\describeReflectedType')]
final class DescribeReflectedTypeTest extends TestCase
{
    /**
     * @psalm-suppress UnusedParam
     */
    #[TestWith(['', ''])]
    #[TestWith(['void', 'void'])]
    #[TestWith(['never', 'never'])]
    #[TestWith(['string', 'string'])]
    #[TestWith([\stdClass::class, \stdClass::class])]
    #[TestWith(['null|string', '?string'])]
    #[TestWith(['null|string|false', 'string|false|null'])]
    #[TestWith(['Stringable&Throwable', 'Stringable&Throwable'])]
    #[TestWith(['Stringable|Throwable', 'Stringable|Throwable'])]
    public function testItDescribesProperty(string $typeDeclaration, string $expected): void
    {
        /** @psalm-suppress ForbiddenCode */
        $function = eval(\sprintf('return function ()%s {};', $typeDeclaration === '' ? '' : ':' . $typeDeclaration));
        \assert($function instanceof \Closure);
        $type = (new \ReflectionFunction($function))->getReturnType();

        $asString = describeReflectedType($type);

        self::assertSame($expected, $asString);
    }
}
