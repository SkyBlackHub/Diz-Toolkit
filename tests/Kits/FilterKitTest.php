<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\FilterKit;

final class FilterKitTest extends TestCase
{
	/**
	 * @covers FilterKit::toIntegerArray
	 */
	public function testToIntegerArray(): void
	{
		$test = [1, 3, null, ' foo  ', '', 'bar', 0, -5, 'bar', '  3  ', 6.7];
		$this->assertSame([1, 3, 0, -5, 3], FilterKit::toIntegerArray($test));
		$this->assertSame([1, 3, 0, -5], FilterKit::toIntegerArray($test, true));
		$this->assertSame([1, 3, 3], FilterKit::toIntegerArray($test, false, 1));
		$this->assertSame([1, 3], FilterKit::toIntegerArray($test, true, 1));
		$this->assertSame([1], FilterKit::toIntegerArray($test, true, 1, 2));
	}

	/**
	 * @covers FilterKit::toFloatArray
	 */
	public function testToFloatArray(): void
	{
		$test = [1, 3.0, null, ' foo  ', '', 'bar', 0, '-5.001', 'bar', '  3.0  ', 6.7];
		$this->assertSame([1.0, 3.0, 0.0, -5.001, 3.0, 6.7], FilterKit::toFloatArray($test));
		$this->assertSame([1.0, 3.0, 0.0, -5.001, 6.7], FilterKit::toFloatArray($test, true));
		$this->assertSame([1.0, 3.0, 3.0, 6.7], FilterKit::toFloatArray($test, false, 1.0));
		$this->assertSame([1.0, 3.0, 6.7], FilterKit::toFloatArray($test, true, 1.0));
		$this->assertSame([1.0], FilterKit::toFloatArray($test, true, 1.0, 2.9));
	}

	/**
	 * @covers FilterKit::toTrimmedArray
	 */
	public function testToTrimmedArray(): void
	{
		$test = [' foo ', '   test', 'BAR', 'test'];
		$this->assertSame(['foo', 'test', 'BAR', 'test'], FilterKit::toTrimmedArray($test));
		$this->assertSame(['foo', 'test', 'BAR'], FilterKit::toTrimmedArray($test, true));
		$this->assertSame(['foo', 'tes', 'BAR'], FilterKit::toTrimmedArray($test, true, 3));
	}

	/**
	 * @covers FilterKit::toUpperCaseArray
	 */
	public function testToUpperCaseArray(): void
	{
		$test = [' foo ', '   test', 'BAR', 'test'];
		$this->assertSame(['FOO', 'TEST', 'BAR', 'TEST'], FilterKit::toUpperCaseArray($test));
		$this->assertSame(['FOO', 'TEST', 'BAR'], FilterKit::toUpperCaseArray($test, true));
		$this->assertSame(['FOO', 'TES', 'BAR'], FilterKit::toUpperCaseArray($test, true, 3));
	}

	/**
	 * @covers FilterKit::toLowerCaseArray
	 */
	public function testToLowerCaseArray(): void
	{
		$test = [' foo ', '   test', 'BAR', 'test'];
		$this->assertSame(['foo', 'test', 'bar', 'test'], FilterKit::toLowerCaseArray($test));
		$this->assertSame(['foo', 'test', 'bar'], FilterKit::toLowerCaseArray($test, true));
		$this->assertSame(['foo', 'tes', 'bar'], FilterKit::toLowerCaseArray($test, true, 3));
	}

	/**
	 * @covers FilterKit::canBeString
	 */
	public function testCanBeString(): void
	{
		$this->assertTrue(FilterKit::canBeString('foo'));
		$this->assertTrue(FilterKit::canBeString(123));
		$this->assertFalse(FilterKit::canBeString([]));

		$object = new \stdClass();
		$this->assertFalse(FilterKit::canBeString($object));

		$object = new class
		{
			public function __toString(): string
			{
				return 'object';
			}
		};
		$this->assertTrue(FilterKit::canBeString($object));
	}

	/**
	 * @covers FilterKit::canBeInteger
	 */
	public function testCanBeInteger(): void
	{
		$this->assertTrue(FilterKit::canBeInteger(42));
		$this->assertTrue(FilterKit::canBeInteger('42'));
		$this->assertTrue(FilterKit::canBeInteger(0));
		$this->assertTrue(FilterKit::canBeInteger(42.0));
		$this->assertTrue(FilterKit::canBeInteger('  -10 '));

		$this->assertTrue(FilterKit::canBeInteger(42.5));
		$this->assertFalse(FilterKit::canBeInteger(42.5, true));

		$this->assertTrue(FilterKit::canBeInteger('00'));
		$this->assertFalse(FilterKit::canBeInteger('00', true));

		$this->assertTrue(FilterKit::canBeInteger('42.0'));
		$this->assertFalse(FilterKit::canBeInteger('a42'));
		$this->assertFalse(FilterKit::canBeInteger(null));
	}

	/**
	 * @covers FilterKit::canBeFloat
	 */
	public function testCanBeFloat(): void
	{
		$this->assertTrue(FilterKit::canBeFloat(42));
		$this->assertTrue(FilterKit::canBeFloat('42'));
		$this->assertTrue(FilterKit::canBeFloat(0));
		$this->assertTrue(FilterKit::canBeFloat(42.0));
		$this->assertTrue(FilterKit::canBeFloat('  -10 '));

		$this->assertTrue(FilterKit::canBeFloat(-0.6545));

		$this->assertTrue(FilterKit::canBeFloat('00'));

		$this->assertTrue(FilterKit::canBeFloat('42.0'));
		$this->assertFalse(FilterKit::canBeFloat('a42'));
		$this->assertFalse(FilterKit::canBeFloat(null));
	}

	/**
	 * @covers FilterKit::toInteger
	 */
	public function testToInteger(): void
	{
		$this->assertSame(123, FilterKit::toInteger('  123 '));
		$this->assertSame(13, FilterKit::toInteger('  123 ', 13, 0, 10));
		$this->assertSame(null, FilterKit::toInteger(0, null, 1));
		$this->assertSame(254, FilterKit::toInteger('0xFE'));
		$this->assertSame(-123, FilterKit::toInteger(' -123 '));
		$this->assertSame(null, FilterKit::toInteger('1.4'), 'Strict');
	}

	/**
	 * @covers FilterKit::toUnsignedInteger
	 */
	public function testToUnsignedInteger(): void
	{
		$this->assertSame(0, FilterKit::toUnsignedInteger('0'));
		$this->assertSame(null, FilterKit::toUnsignedInteger(-7));
		$this->assertSame(123, FilterKit::toUnsignedInteger('  123 '));
		$this->assertSame(13, FilterKit::toUnsignedInteger('  123 ', 13, 0, 10));
		$this->assertSame(null, FilterKit::toUnsignedInteger(0, null, 1));
		$this->assertSame(254, FilterKit::toUnsignedInteger('0xFE'));
		$this->assertSame(null, FilterKit::toUnsignedInteger(' -123 '));
		$this->assertSame(null, FilterKit::toUnsignedInteger('1.4'), 'Strict');
	}

	/**
	 * @covers FilterKit::toFloat
	 */
	public function testToFloat(): void
	{
		$this->assertSame(123.0, FilterKit::toFloat('  123 '));
		$this->assertSame(123.4, FilterKit::toFloat('  123.4 '));
		$this->assertSame(13.0, FilterKit::toFloat('  123.4 ', 13.0, 0.6, 10));
		$this->assertSame(null, FilterKit::toFloat(0.99, null, 1));
		$this->assertSame(-123.4, FilterKit::toFloat(' -123.4 '));
		$this->assertSame(1.4, FilterKit::toFloat('1.4'), 'Non Strict');
	}

	/**
	 * @covers FilterKit::toDateTime
	 */
	public function testToDateTime(): void
	{
		$time = new \DateTime();
		$this->assertSame($time, FilterKit::toDateTime($time));
		$this->assertEquals(new \DateTime('@15707'), FilterKit::toDateTime('15707'));
		$this->assertSame(null, FilterKit::toDateTime('  123.4 '));
		$this->assertSame(null, FilterKit::toDateTime('foo'));
		$time = new \DateTime('2022-12-25 12:34:56');
		$this->assertEquals($time, FilterKit::toDateTime('fourth Sun, Dec 2022 12:34:56'));
		$time->setTimezone(new \DateTimeZone('Europe/London'));
		$this->assertEquals($time, FilterKit::toDateTime('25 Dec 2022 12:34:56 Europe/London'));
		$this->assertNotEquals($time, FilterKit::toDateTime('25 Dec 2022 12:34:56 -0600'));
	}
}