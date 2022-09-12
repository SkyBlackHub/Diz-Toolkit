<?php

namespace Diz\Toolkit\Iterators;

/**
 * Iterator for diapasons of integer values
 * Iterates from the start value to a value not exceeding the end value with a given step
 */
class DiapasonIterator implements \Iterator, \Countable
{
	private int $start;
	private int $end;
	private int $step;

	private int $count;
	private int $iteration;

	public function __construct(int $start, int $end, int $step = 1, int $iteration = 0)
	{
		$this->start = $start;
		$this->end = $end;
		$this->step = max(1, $step);
		$this->count = $this->getRange() / $this->step + 1;
		$this->iteration = min(max(0, $iteration), $this->count);
	}

	public function getStart(): int
	{
		return $this->start;
	}

	public function getEnd(): int
	{
		return $this->end;
	}

	public function getStep(): int
	{
		return $this->step;
	}

	public function getCount(): int
	{
		return $this->count;
	}

	public function getIteration(): int
	{
		return $this->iteration;
	}

	public function getProgress(): int
	{
		return $this->step * $this->iteration;
	}

	public function getValue(): int
	{
		if ($this->isReversed()) {
			return $this->start - $this->getProgress();
		}
		return $this->start + $this->getProgress();
	}

	public function getRange(): int
	{
		return abs($this->end - $this->start);
	}

	public function isValid(): bool
	{
		return $this->iteration < $this->count;
	}

	public function isReversed(): bool
	{
		return $this->start > $this->end;
	}

	public function isAtStart(): bool
	{
		return $this->iteration == 0;
	}

	public function isAtLast(): bool
	{
		return $this->iteration == $this->count - 1;
	}

	public function isAtEnd(): bool
	{
		return $this->getValue() == $this->end;
	}

	public function getIterationsPercent(): float
	{
		$count = $this->getCount();
		return $count ? round(($this->getIteration() + 1) / $count * 100, 2) : 0.0;
	}

	public function getProgressPercent(): float
	{
		$length = $this->getRange();
		return $length ? round($this->getProgress() / $length * 100, 2) : 0.0;
	}

	public function toArray(): array
	{
		return range($this->start, $this->end, $this->isReversed() ? - $this->step : $this->step);
	}

	public function getMaxProgress(): int
	{
		return $this->step * ($this->count - 1);
	}

	public function getUpperBoundary(): int
	{
		return $this->isReversed() ? $this->start : $this->end;
	}

	public function getLowerBoundary(): int
	{
		return $this->isReversed() ? $this->end : $this->start;
	}

	public function getHighestValue(): int
	{
		return $this->isReversed() ? $this->start : $this->start + $this->getMaxProgress();
	}

	public function getLowestValue(): int
	{
		return $this->isReversed() ? $this->start - $this->getMaxProgress() : $this->start;
	}

	public function current()
	{
		return $this->getValue();
	}

	public function next(): void
	{
		$this->iteration = min($this->iteration + 1, $this->count);
	}

	public function previous(): void
	{
		$this->iteration = max($this->iteration - 1, 0);
	}

	public function change(int $delta): void
	{
		$this->iteration = max(0, min($this->iteration + $delta, $this->count));
	}

	public function key()
	{
		return $this->getIteration();
	}

	public function valid(): bool
	{
		return $this->isValid();
	}

	public function rewind(): void
	{
		$this->iteration = 0;
	}

	public function count(): int
	{
		return $this->getCount();
	}
}