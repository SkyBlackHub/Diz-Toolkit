<?php

namespace Diz\Toolkit\Tests\Iterators;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Iterators\DiapasonIterator;

final class DiapasonIteratorTest extends TestCase
{
	public function testBasic(): void
	{
		$iterator = new DiapasonIterator(1, 5);
		$expected = [1, 2, 3, 4, 5];

		$this->assertTrue($iterator->isAtStart(), 'Is At The Start');
		$this->assertSame(5, $iterator->getCount(), 'Count');
		$this->assertSame(4, $iterator->getRange(), 'Range');
		$this->assertSame($expected, $iterator->toArray(), 'To Array');

		foreach ($iterator as $index => $value) {
			$this->assertSame($expected[$value - 1] ?? null, $value, 'Foreach: ' . $index);
		}

		$iterator = new DiapasonIterator(0, 5, 3);
		$this->assertSame(0, $iterator->getLowerBoundary(), 'Lower Boundary');
		$this->assertSame(5, $iterator->getUpperBoundary(), 'Upper Boundary');
		$this->assertSame(3, $iterator->getMaxProgress(), 'Max Progress');
		$this->assertSame(0, $iterator->getLowestValue(), 'Lowest Value');
		$this->assertSame(3, $iterator->getHighestValue(), 'Highest Value');

		$iterator->next();
		$this->assertSame(1, $iterator->getIteration(), 'Iteration');
		$this->assertSame(3, $iterator->getProgress(), 'Progress');
		$this->assertTrue($iterator->isAtLast(), 'Is At The Last');
		$this->assertSame(100.0, $iterator->getIterationsPercent(), 'Iteration Percent');
		$this->assertSame(60.0, $iterator->getProgressPercent(), 'Progress Percent');

		$iterator = new DiapasonIterator(0, 10, 3);
		$iterator->next();
		$this->assertSame(3, $iterator->getValue());
		$iterator->change(2);
		$this->assertSame(9, $iterator->getValue());
		$iterator->previous();
		$this->assertSame(6, $iterator->getValue());
		$iterator->change(-2);
		$this->assertSame(0, $iterator->getValue());
	}

	public function testStep(): void
	{
		$iterator = new DiapasonIterator(0, 9, 3);

		static $foreach_items = [0, 3, 6, 9];

		$this->assertTrue($iterator->isAtStart(), 'Is At The Start');
		$this->assertSame(4, $iterator->getCount(), 'Count');
		$this->assertSame(9, $iterator->getRange(), 'Range');

		foreach ($iterator as $index => $value) {
			$this->assertSame($foreach_items[$index] ?? null, $value, 'Foreach: ' . $index);
		}

		$this->assertFalse($iterator->isValid(), 'Is Valid');
		$iterator->previous();
		$this->assertTrue($iterator->isValid(), 'Is Valid');
		$this->assertTrue($iterator->isAtEnd(), 'Is At The End');
	}

	public function testReverse(): void
	{
		$iterator = new DiapasonIterator(12, 2, 2);

		static $expected = [12, 10, 8, 6, 4, 2];

		$this->assertTrue($iterator->isAtStart(), 'Is At The Start');
		$this->assertSame(6, $iterator->getCount(), 'Count');
		$this->assertSame(10, $iterator->getRange(), 'Range');
		$this->assertSame($expected, $iterator->toArray(), 'To Array');

		foreach ($iterator as $index => $value) {
			$this->assertSame($expected[$index] ?? null, $value, 'Foreach: ' . $index);
		}

		$iterator = new DiapasonIterator(5, 0, 3);
		$this->assertSame([5, 2], $iterator->toArray(), 'To Array');
		$this->assertSame(0, $iterator->getLowerBoundary(), 'Lower Boundary');
		$this->assertSame(5, $iterator->getUpperBoundary(), 'Upper Boundary');
		$this->assertSame(3, $iterator->getMaxProgress(), 'Max Progress');
		$this->assertSame(2, $iterator->getLowestValue(), 'Lowest Value');
		$this->assertSame(5, $iterator->getHighestValue(), 'Highest Value');

		$iterator->next();
		$this->assertTrue($iterator->isAtLast(), 'Is At The Last');
		$this->assertFalse($iterator->isAtEnd(), 'Is At The End');
		$iterator->next();
		$this->assertFalse($iterator->isValid(), 'Is Not Valid');
	}
}