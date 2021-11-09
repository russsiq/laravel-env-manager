<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Russsiq\EnvManager\Domain\Model\Variable;
use Russsiq\EnvManager\Domain\Model\VariableException;

/**
 * @cmd vendor/bin/phpunit Tests\Unit\VariableTest.php
 * @coversDefaultClass \Russsiq\EnvManager\Domain\Model\Variable
 */
class VariableTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function test_successfully_initiated(): void
    {
        $variable = new Variable(
            $key = 'KEY',
            $value = 'Value',
        );

        $this->assertEquals($key, $variable->key());
        $this->assertEquals($value, $variable->value());
        $this->assertIsString($variable->value());
    }

    /**
     * @covers ::createWithEmptyValue
     */
    public function test_successfully_initiated_with_empty_value(): void
    {
        $variable = Variable::createWithEmptyValue(
            $key = 'KEY'
        );

        $this->assertEquals($key, $variable->key());
        $this->assertEquals('', $variable->value());
        $this->assertEmpty($variable->value());
    }

    /**
     * @covers ::__construct
     */
    public function test_successfully_initiated_with_nullable_value(): void
    {
        $variable = new Variable(
            $key = 'KEY',
            $value = (string) null,
        );

        $this->assertEquals($key, $variable->key());
        $this->assertEquals($value, $variable->value());
        $this->assertEmpty($variable->value());
    }

    /**
     * @covers ::__construct
     */
    public function test_successfully_initiated_with_boolean_value(): void
    {
        $variable = new Variable(
            $key = 'KEY',
            $value = (string) true,
        );

        $this->assertEquals($key, $variable->key());
        $this->assertEquals($value, $variable->value());
        $this->assertTrue((bool) $variable->value());

        $variable = new Variable(
            $key = 'KEY',
            $value = (string) false,
        );

        $this->assertEquals($key, $variable->key());
        $this->assertEquals($value, $variable->value());
        $this->assertFalse((bool) $variable->value());
    }

    /**
     * @covers ::__construct
     */
    public function test_value_in_variable_should_be_quoted(): void
    {
        $variable = new Variable(
            'KEY',
            'Some Value',
        );

        $this->assertEquals('KEY="Some Value"', (string) $variable);
    }

    /**
     * @covers ::__construct
     */
    public function test_variable_key_should_be_uppercase(): void
    {
        $arguments = [
            'key',
            'value',
        ];

        $this->expectException(VariableException::class);
        $this->expectExceptionMessage(sprintf(
            "Format of key [%s] is not supported for environment variable.",
            reset($arguments)
        ));

        $variable = new Variable(
            ...$arguments
        );
    }

    /**
     * @covers ::__construct
     */
    public function test_variable_key_and_value_should_be_trimmed(): void
    {
        $variable = new Variable(
            " KEY ",
            "\tValue\n",
        );

        $this->assertEquals('KEY=Value', (string) $variable);
    }

    /**
     * @covers ::equals
     */
    public function test_compare_two_variables(): void
    {
        $firstVariable = new Variable(
            'KEY',
            'Value',
        );

        $secondVariable = new Variable(
            'KEY',
            'Value',
        );

        $this->assertTrue($firstVariable->equals($secondVariable));
    }

    /**
     * @covers ::__toString
     */
    public function test_variable_should_be_stringable(): void
    {
        $variable = new Variable(
            'KEY',
            'Value',
        );

        $this->assertEquals('KEY=Value', (string) $variable);
    }
}
