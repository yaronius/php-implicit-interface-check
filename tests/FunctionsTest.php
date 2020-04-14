<?php

declare(strict_types=1);

namespace yaronius\ImplicitInterface\tests;

use PHPUnit\Framework\TestCase;

use function yaronius\ImplicitInterface\class_provides;

class FunctionsTest extends TestCase
{
    public function testClassProvidesFunction()
    {
        $this->assertTrue(class_provides(AbstractFoo::class, FooInterface::class));
        $this->assertTrue(class_provides(Foo::class, FooInterface::class));

        $this->assertTrue(class_provides(new Foo, FooInterface::class));

        $this->assertTrue(class_provides(new Bar, BarInterface::class));
        $this->assertFalse(class_provides(new Bar, BazInterface::class));
    }
}

abstract class AbstractFoo
{
    abstract public function doDomething(string $param, int $param2): int;
}

class Foo extends AbstractFoo
{
    public function doDomething(string $param, int $param2): int
    {
        return 42;
    }
}

interface FooInterface
{
    function doDomething(string $param, int $param2): int;
}

class Bar
{
    function doDomethingElse(array $param, callable $param2, $untypedParam = null)
    {
        return 'bar';
    }
}

interface BarInterface
{
    function doDomethingElse(array $param, callable $param2, $untypedParam = null);
}

interface BazInterface
{
    function doDomethingElse(array $param, callable $param2, $untypedParam);
}
