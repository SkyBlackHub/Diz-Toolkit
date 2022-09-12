<?php

namespace Diz\Toolkit\Iterators;

class MultiByteIterator implements \Iterator
{
	private int $position = 0;
	private int $index = 0;
	private string $text;
	private ?string $current;

	public function __construct(string $text)
	{
		$this->text = $text;
		$this->readCurrent();
	}

	public static function calculateCharSize(string $char): int
	{
		$char = ord($char);
		if ($char < 128) {
			return 1;
		}
		if ($char < 224) {
			return 2;
		}
		if ($char < 240) {
			return 3;
		}
		return 4;
	}

	public function getPosition(): string
	{
		return $this->position;
	}

	public function getIndex(): string
	{
		return $this->index;
	}

	public function getText(): string
	{
		return $this->text;
	}

	private function readCurrent(): void
	{
		$this->current = $this->text[$this->position] ?? null;
		if ($this->current) {
			$size = static::calculateCharSize($this->current);
			if ($size > 1) {
				$this->current = substr($this->text, $this->position, $size);
			}
		}
	}

	/**
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function current()
	{
		return $this->current;
	}

	/**
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function key()
	{
		return $this->index;
	}

	public function next(): void
	{
		if ($this->valid()) {
			$this->position += strlen($this->current);
			++$this->index;
			$this->readCurrent();
		}
	}

	public function rewind(): void
	{
		$this->position = 0;
		$this->index = 0;
		$this->readCurrent();
	}

	public function valid(): bool
	{
		return $this->current !== null;
	}
}