<?php

declare(strict_types=1);

namespace Typhoon\Describer;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Typhoon\Describer\Fixture\SomeEnum;

#[CoversFunction('Typhoon\Describer\describeValueType')]
final class DescribeValueTypeTest extends TestCase
{
    #[TestWith([null, 'null'])]
    #[TestWith([true, 'true'])]
    #[TestWith([false, 'false'])]
    #[TestWith([1, '1'])]
    #[TestWith([-55, '-55'])]
    #[TestWith([1.5, '1.5'])]
    #[TestWith([-0.5, '-0.5'])]
    #[TestWith(['a', "'a'"])]
    #[TestWith(["a'b", "'a\\'b'"])]
    #[TestWith([[], 'list{}'])]
    #[TestWith([[1, 2, 3], 'list{1, 2, 3}'])]
    #[TestWith([['a' => 'b'], "array{'a': 'b'}"])]
    #[TestWith([['a' => 'b', 'c'], "array{'a': 'b', 0: 'c'}"])]
    #[TestWith([SomeEnum::A, 'Typhoon\Describer\Fixture\SomeEnum::A'])]
    #[TestWith([new \stdClass(), 'stdClass'])]
    #[TestWith([STDIN, 'resource'])]
    public function test(mixed $value, string $expected): void
    {
        $type = describeValueType($value);

        self::assertSame($expected, $type);
    }

    public function testAnonymousClassObject(): void
    {
        $object = new class {};

        $type = describeValueType($object);

        self::assertSame('object', $type);
    }
}
