<?php

namespace Diz\Toolkit\Tests\Iterators;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Iterators\SingleByteIterator;

final class SingleByteIteratorTest extends TestCase
{
	public function testBasic(): void
	{
		$text = 'test';
		$iterator = new SingleByteIterator($text);

		static $foreach_chars = ['t', 'e', 's', 't'];

		foreach ($iterator as $index => $char) {
			$this->assertSame($foreach_chars[$index] ?? null, $iterator->current(), 'Foreach: ' . $index);
		}

		$length = strlen($text);
		for ($index = 0; $index < $length; ++$index) {
			$this->assertSame($foreach_chars[$index] ?? null, $text[$index], 'Regular access: ' . $index);
		}

		$iterator->rewind();
		$this->assertSame('t', $iterator->current(), 'Rewind');
	}
}