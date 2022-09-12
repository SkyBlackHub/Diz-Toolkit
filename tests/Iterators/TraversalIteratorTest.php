<?php

namespace Diz\Toolkit\Tests\Iterators;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Iterators\DiapasonIterator;
use Diz\Toolkit\Iterators\TraversalIterator;

final class TraversalIteratorTest extends TestCase
{
	public function testFlat(): void
	{
		$test = [1, 'foo' => 'bar', ['sub' => 'zero'], new \stdClass(), 2];

		$iterator = new TraversalIterator($test);

		$this->assertTrue($iterator->isAtStart(), 'Is At The Start');
		$this->assertSame(5, $iterator->getCount(), 'Count');

		foreach ($iterator as $key => $value) {
			$this->assertSame($test[$key], $value, 'Foreach: ' . $key);
		}
	}

	public function testNested(): void
	{
		$diapason = new DiapasonIterator(1, 3);
		$diapason->next();
		$diapason->next();
		$diapason->next();
		$this->assertFalse($diapason->isValid());

		$test = ['i0' => 1, 'foo' => 'bar', 'i1' => ['sub' => 'zero'], new DiapasonIterator(1, 3), 'i2' => 2];
		$expected = ['i0' => 1, 'foo' => 'bar', 'i1' => ['sub' => 'zero'], 1, 2, 3, 'i2' => 2];

		$iterator = new TraversalIterator($test);

		$this->assertTrue($iterator->isAtStart(), 'Is At The Start');
		$this->assertSame(7, $iterator->getCount(), 'Count');

		foreach ($iterator as $key => $value) {
			$this->assertSame($expected[$key] ?? null, $value, 'Foreach: ' . $key);
		}
	}
}