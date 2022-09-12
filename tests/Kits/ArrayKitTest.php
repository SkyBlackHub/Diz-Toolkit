<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\ArrayKit;

final class ArrayKitTest extends TestCase
{
	/**
	 * @covers ArrayKit::isAssociative
	 */
	public function testIsAssociative(): void
	{
		$this->assertTrue(ArrayKit::isAssociative(['a' => 1, 'b' => 2, 'c' => 3]));
		$this->assertTrue(ArrayKit::isAssociative([2 => 'a', 4 => 'b', 0 => 'c']));
		$this->assertTrue(ArrayKit::isAssociative([2 => 'a','b', 'c']));
		$this->assertFalse(ArrayKit::isAssociative([1, 2, 3]));
		$this->assertFalse(ArrayKit::isAssociative([0 => 'a', 1 => 'b', 2 => 'c']));
		$this->assertFalse(ArrayKit::isAssociative([]));
		$this->assertFalse(ArrayKit::isAssociative([0 => 'a','b', 'c']));
	}

	/**
	 * @covers ArrayKit::getLastElement
	 */
	public function testGetLastElement(): void
	{
		$this->assertSame('bar', ArrayKit::getLastElement(['foo', 'bar']));
		$this->assertSame(null, ArrayKit::getLastElement([]));
		$this->assertSame('bar', ArrayKit::getLastElement([123, 'foo' => 'bar']));
		$this->assertSame(456, ArrayKit::getLastElement([123, 'foo' => 'bar', 456]));
	}

	/**
	 * @covers ArrayKit::getFirstElement
	 */
	public function testGetFirstElement(): void
	{
		$this->assertSame('foo', ArrayKit::getFirstElement(['foo', 'bar']));
		$this->assertSame(null, ArrayKit::getFirstElement([]));
		$this->assertSame('bar', ArrayKit::getFirstElement(['foo' => 'bar', 123]));
		$this->assertSame(123, ArrayKit::getFirstElement([123, 'foo' => 'bar', 456]));
	}

	/**
	 * @covers ArrayKit::getString
	 * @covers ArrayKit::getUsefulString
	 * @covers ArrayKit::getInteger
	 * @covers ArrayKit::getFloat
	 * @covers ArrayKit::getBoolean
	 */
	public function testGetByType(): void
	{
		$data = [
			'foo' => ' BAR ',
			'int' => 555,
			'float' => 77.7,
			'sup' => [
				'test' => 123
			],
			'true' => true,
			'false' => false
		];

		$this->assertSame(' BAR ', ArrayKit::getString($data, 'foo'));
		$this->assertSame('BAR', ArrayKit::getUsefulString($data, 'foo'));
		$this->assertSame('BAR', ArrayKit::getString($data, 'not_foo', 'BAR'));
		$this->assertSame(null, ArrayKit::getString($data, 'not_foo'));

		$this->assertSame(null, ArrayKit::getInteger($data, 'nope'));
		$this->assertSame(123, ArrayKit::getInteger($data, 'nope', 123));
		$this->assertSame(555, ArrayKit::getInteger($data, 'int'));
		$this->assertSame(77, ArrayKit::getInteger($data, 'float'));

		$this->assertSame(null, ArrayKit::getFloat($data, 'nope'));
		$this->assertSame(55.5, ArrayKit::getFloat($data, 'nope', 55.5));
		$this->assertSame(77.7, ArrayKit::getFloat($data, 'float'));

		$this->assertSame(true, ArrayKit::getBoolean($data, 'true'));
		$this->assertSame(false, ArrayKit::getBoolean($data, 'false'));
		$this->assertSame(false, ArrayKit::getBoolean($data, 'zilch', false));
		$this->assertSame(null, ArrayKit::getBoolean($data, 'float'));
	}
}