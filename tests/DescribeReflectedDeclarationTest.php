<?php

declare(strict_types=1);

namespace Typhoon\Describer;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\TestCase;

#[CoversFunction('Typhoon\Describer\describeReflectedDeclaration')]
final class DescribeReflectedDeclarationTest extends TestCase
{
    public function testItDescribesReflectionFunctionFromName(): void
    {
        $reflection = new \ReflectionFunction('trim');

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('function `trim`', $description);
    }

    public function testItDescribesReflectionFunctionFromClosure(): void
    {
        $reflection = new \ReflectionFunction(trim(...));

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('function `trim`', $description);
    }

    public function testItDescribesReflectionMethod(): void
    {
        $reflection = new \ReflectionMethod($this, 'testItDescribesReflectionMethod');

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('method `%s`', __METHOD__), $description);
    }

    public function testItDescribesReflectionFunctionFromMethodClosure(): void
    {
        $reflection = new \ReflectionFunction($this->testItDescribesReflectionFunctionFromMethodClosure(...));

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('method `%s`', __METHOD__), $description);
    }

    public function testItDescribesAnonymousFunction(): void
    {
        $reflection = new \ReflectionFunction(static fn(): string => 'a');

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('anonymous function at `%s:%d`', __FILE__, __LINE__ - 4), $description);
    }

    public function testItDescribesAnonymousFunctionWithoutFile(): void
    {
        $function = static fn(): string => 'a';
        $reflection = new class ($function) extends \ReflectionFunction {
            /** @psalm-external-mutation-free */
            public function getFileName(): string|false
            {
                return false;
            }
        };

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('anonymous function', $description);
    }

    public function testItDescribesAnonymousFunctionWithoutLine(): void
    {
        $function = static fn(): string => 'a';
        $reflection = new class ($function) extends \ReflectionFunction {
            /** @psalm-external-mutation-free */
            public function getStartLine(): int|false
            {
                return false;
            }
        };

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('anonymous function at `%s`', __FILE__), $description);
    }

    public function testItDescribesParameter(): void
    {
        $reflection = new \ReflectionParameter(trim(...), 'string');

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('parameter #0 `string` of function `trim`', $description);
    }

    public function testItDescribesReflectionClass(): void
    {
        $reflection = new \ReflectionClass(self::class);

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('class `Typhoon\Describer\DescribeReflectedDeclarationTest`', $description);
    }

    public function testItDescribesAnonymousClass(): void
    {
        $reflection = new \ReflectionClass(new class {});

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('anonymous class at `%s:%d`', __FILE__, __LINE__ - 4), $description);
    }

    public function testItDescribesAnonymousClassWithoutLine(): void
    {
        $object = new class {};
        $reflection = new
        /** @extends \ReflectionClass<object> */
        class ($object) extends \ReflectionClass {
            /** @psalm-external-mutation-free */
            public function getStartLine(): int|false
            {
                return false;
            }
        };

        $description = describeReflectedDeclaration($reflection);

        self::assertSame(\sprintf('anonymous class at `%s`', __FILE__), $description);
    }

    public function testItDescribesAnonymousClassWithoutFile(): void
    {
        $object = new class {};
        $reflection = new
        /** @extends \ReflectionClass<object> */
        class ($object) extends \ReflectionClass {
            /** @psalm-external-mutation-free */
            public function getFileName(): string|false
            {
                return false;
            }
        };

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('anonymous class', $description);
    }

    public function testItDescribesProperty(): void
    {
        $reflection = new \ReflectionProperty(\Exception::class, 'message');

        $description = describeReflectedDeclaration($reflection);

        self::assertSame('property `message` of class `Exception`', $description);
    }
}
