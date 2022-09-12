<?php

namespace Diz\Toolkit\Iterators;

/**
 * Iterator for arrays of mixed data and iterable objects
 * Iterates over the passed array, recursively iterating over the elements that inherit the \Iterator interface
 */
class TraversalIterator implements \Iterator, \Countable
{
	private array $data;

	public function __construct(array $data)
	{
		$this->data = $data;
		reset($this->data);
	}

	public function getData(): array
	{
		return $this->data;
	}

	public function getValue()
	{
		$current = current($this->data);
		if ($current instanceof \Iterator) {
			return $current->current();
		}
		return $current;
	}

	public function getKey()
	{
		$current = current($this->data);
		if ($current instanceof \Iterator) {
			return $current->key();
		}
		return key($this->data);
	}

	public function getOriginKey()
	{
		return key($this->data);
	}

	public function getCount(): int
	{
		$result = 0;
		foreach ($this->data as $item) {
			if ($item instanceof \Iterator) {
				if ($item instanceof \Countable) {
					$result += $item->count();
				} else {
					foreach ($item as $ignored) {
						++$result;
					}
				}
			} else {
				++$result;
			}
		}
		return $result;
	}

	public function isAtStart(): bool
	{
		return key($this->data) == array_key_first($this->data);
	}

	public function isAtEnd(): bool
	{
		return key($this->data) == array_key_last($this->data);
	}

	public function toArray(bool $preserve_keys = true): array
	{
		return iterator_to_array($this, $preserve_keys);
	}

	public function current()
	{
		return $this->getValue();
	}

	public function next(): void
	{
		$current = current($this->data);
		if ($current instanceof \Iterator) {
			$current->next();
			if ($current->valid()) {
				return;
			}
		}
		$this->moveToValid(next($this->data));
	}

	public function key()
	{
		return $this->getKey();
	}

	public function valid(): bool
	{
		return key($this->data) !== null;
	}

	public function rewind(): void
	{
		$this->moveToValid(reset($this->data));
	}

	private function moveToValid($current): void
	{
		while (key($this->data) !== null) {
			if ($current instanceof \Iterator) {
				$current->rewind();
				if ($current->valid()) {
					return;
				}
			} else {
				return;
			}
			$current = next($this->data);
		}
	}

	public function count(): int
	{
		return $this->getCount();
	}
}