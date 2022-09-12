<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\ParseKit;

use Diz\Toolkit\Iterators\DiapasonIterator;
use Diz\Toolkit\Iterators\TraversalIterator;

final class ParseKitTest extends TestCase
{
	/**
	 * @covers ParseKit::toSeries
	 */
	public function testToSeries(): void
	{
		$this->assertEquals(new TraversalIterator([1, 2, 3]), ParseKit::toSeries('1, 2, 3'));
		$this->assertEquals(new TraversalIterator([1, 2, 3]), ParseKit::toSeries([1, 2, 3]));
		$this->assertEquals([1, 2, 3], ParseKit::toSeries('1-3')->toArray());
	}
	/**
	 * @covers ParseKit::toDiapason
	 */
	public function testToDiapason(): void
	{
		$this->assertEquals(new DiapasonIterator(1, 9, 3), ParseKit::toDiapason('1-9:3'));
		$this->assertEquals(new DiapasonIterator(2, 6, 2), ParseKit::toDiapason('2 + 4 : 2'));
		$this->assertEquals(new DiapasonIterator(12, 1), ParseKit::toDiapason(' 12 - 1 '));
		$this->assertEquals(new DiapasonIterator(-8, -12, 2), ParseKit::toDiapason(' - 8  - - 12 :  2  '));
		$this->assertSame(null, ParseKit::toDiapason(' 20 - 4 : - 2 '), 'Step value can only be a positive number');

		$this->assertEquals(new DiapasonIterator(1, 9, 3), ParseKit::toDiapason([1, 9, 3]));
		$this->assertEquals(new DiapasonIterator(1, 10, 2), ParseKit::toDiapason(['start' => 1, 'end' => 10, 'step' => 2]));
		$this->assertEquals(new DiapasonIterator(12, 1), ParseKit::toDiapason([12, 'end' => 1, 'step' => 'foo']));
		$this->assertEquals(new DiapasonIterator(-8, -12, 2), ParseKit::toDiapason([-8, 'step' => 2, -12]));

		$this->assertEquals(new DiapasonIterator(4, 4), ParseKit::toDiapason('4'));
		$this->assertEquals(new DiapasonIterator(4, 5), ParseKit::toDiapason([4.5, 5.06]));
	}

	/**
	 * @covers ParseKit::toNumberArray
	 */
	public function testToNumberArray(): void
	{
		$this->assertSame([100], ParseKit::toNumberArray('100'));
		$this->assertSame([33.3], ParseKit::toNumberArray('33.3'));
		$this->assertSame([100, 33.3], ParseKit::toNumberArray([100, 33.3, 100], null, true));

		$this->assertSame([1, 2, 3, 2, 1.1], ParseKit::toNumberArray('1|2|foo|3.0|2.0|1.1', '|'));

		$this->assertSame([100, -200, 33.3], ParseKit::toNumberArray('100, -200, 33.3, 100', ',', true));
	}

	/**
	 * @covers ParseKit::toSizes
	 */
	public function testToSizes(): void
	{
		$this->assertSame([[100, 200], [300, 500]], ParseKit::toSizes('100x200, 300x500, foo, bar'));
		$this->assertSame([[300, 500]], ParseKit::toSizes('100x200x300, 300x500, fooxbar'));

		$this->assertSame([[100, 200], [300, 500]], ParseKit::toSizes('100x200, 300x500, 100x200', true));

		$this->assertSame([[100, 200]], ParseKit::toSizes('100x200, 300x500, 400x200', false, 100, 300));

		$this->assertSame([[[100, 200], [300, 500]], [[2, 2], [4, 4]]], ParseKit::toSizes('100x200, 300x500 | 2x2, 4x4', false, null, null, ['|', ',', 'x']));
	}

	/**
	 * @covers ParseKit::toLinearArray
	 */
	public function testToLinearArray(): void
	{
		$this->assertSame(['300', '500', 'foo', ' bar '], ParseKit::toLinearArray('300,500,foo, bar ', ','));
		$this->assertSame(['foo'], ParseKit::toLinearArray('foo'));
		$this->assertSame([300, 500, 'foo', 'bar'], ParseKit::toLinearArray('300,500,foo,500, bar ', ',', true, function($value) {
			if (is_numeric($value)) {
				return $value + 0;
			}
			return trim($value);
		}));
	}

	/**
	 * @covers ParseKit::toNestedArray
	 */
	public function testToNestedArray(): void
	{
		$this->assertSame([[100, 200], ['foo', 'bar'], [1, 2, 3, 4]], ParseKit::toNestedArray('100,200| foo , bar | 1 - 4', ['|', ','], function($value, string $separator, int $level) {
			if ($level != 0) {
				return $value;
			}
			if (is_numeric($value)) {
				return $value + 0;
			}
			return trim($value);
		}, function($value, string $separator, int &$level) {
			if ($level != 1) {
				return $value;
			}
			if (strpos($value, '-') !== false) {
				$range = explode('-', $value);
				if (count($range) == 2) {
					$level = -1;
					return range($range[0], $range[1]);
				}
			}
			return $value;
		}));
	}
}