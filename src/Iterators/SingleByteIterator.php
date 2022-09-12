<?php

namespace Diz\Toolkit\Iterators;

class SingleByteIterator implements \Iterator
{
	private int $index = 0;
	private string $text;
	private ?string $current;

	public function __construct(string $text)
	{
		$this->text = $text;
		$this->readCurrent();
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
		$this->current = $this->text[$this->index] ?? null;
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
			++$this->index;
			$this->readCurrent();
		}
	}

	public function rewind(): void
	{
		$this->index = 0;
		$this->readCurrent();
	}

	public function valid(): bool
	{
		return $this->current !== null;
	}
}