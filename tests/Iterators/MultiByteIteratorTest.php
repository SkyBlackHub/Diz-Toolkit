<?php

namespace Diz\Toolkit\Tests\Iterators;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Iterators\MultiByteIterator;

final class MultiByteIteratorTest extends TestCase
{
	public function testBasic(): void
	{
		$text = 'áçя';
		$iterator = new MultiByteIterator($text);

		static $foreach_chars = ['á', 'ç', 'я'];

		foreach ($iterator as $index => $char) {
			$this->assertSame($foreach_chars[$index] ?? null, $iterator->current(), 'Foreach: ' . $index);
		}

		$length = strlen($text);
		for ($index = 0; $index < $length; ++$index) {
			$this->assertNotSame($foreach_chars[$index] ?? null, $text[$index], 'Regular access: ' . $index);
		}

		$iterator->rewind();
		$this->assertSame('á', $iterator->current(), 'Rewind');
	}
}