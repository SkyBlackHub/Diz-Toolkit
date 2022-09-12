<?php

namespace Diz\Toolkit\Tools;

use Diz\Toolkit\Kits\FilterKit;

class Path implements \IteratorAggregate, \ArrayAccess
{
	public const DELIMITER_DOT       = '.';
	public const DELIMITER_COMMA     = ',';
	public const DELIMITER_SLASH     = '/';
	public const DELIMITER_BACKSLASH = '\\';

	public const DEFAULT_DELIMITER = self::DELIMITER_SLASH;

	public const OPTION_NONE            = 0b00000000;
	public const OPTION_TRIM            = 0b00000001;
	public const OPTION_CLEAN           = 0b00000010;

	public const OPTION_LOWER_CASE      = 0b00000100;

	public const OPTION_REGEX_DELIMITER = 0b00010000;

	public const OPTIONS_DEFAULT        = self::OPTION_TRIM | self::OPTION_CLEAN;
	public const OPTIONS_UNIFIED        = self::OPTIONS_DEFAULT | self::OPTION_LOWER_CASE;

	public const TRAVERSE_NONE      = 0b00000000;
	public const TRAVERSE_WILDCARDS = 0b00000010;
	public const TRAVERSE_COVERAGE  = 0b00000100;
	public const TRAVERSE_FALLBACK  = 0b00001000;

	private array $nodes = [];

	public function __construct($path = null, $delimiter = self::DEFAULT_DELIMITER, int $options = self::OPTIONS_DEFAULT)
	{
		if ($path !== null) {
			$this->setPath($path, $delimiter, $options);
		}
	}

	public function getIterator(): \ArrayIterator
	{
		return new \ArrayIterator($this->nodes);
	}

	public function offsetSet($offset, $value): void
	{
		if ($offset === null) {
			$this->append($value);
		} else if (FilterKit::canBeInteger($offset) && FilterKit::canBeString($value)) {
			$this->setAt($offset, $value);
		}
	}

	public function offsetExists($offset): bool
	{
		return FilterKit::canBeInteger($offset) && $this->isExists($offset);
	}

	public function offsetUnset($offset): void
	{
		if (FilterKit::canBeInteger($offset)) {
			$this->removeAt($offset);
		}
	}

	public function offsetGet($offset)
	{
		return FilterKit::canBeInteger($offset) ? $this->getAt($offset) : null;
	}

	public function getAll(): array
	{
		return $this->nodes;
	}

	public function getAt(int $index): ?string
	{
		return $this->nodes[$index] ?? null;
	}

	public function setAt(int $index, string $value): self
	{
		if (isset($this->nodes[$index])) {
			$this->nodes[$index] = $value;
		}
		return $this;
	}

	public function append(string $value): self
	{
		$this->nodes[] = $value;
		return $this;
	}

	public function prepend(string $value): self
	{
		array_unshift($this->nodes, $value);
		return $this;
	}

	public function insertAt(int $index, string $value): self
	{
		array_splice($this->nodes, $index, 0, $value);
		return $this;
	}

	public function isEmpty(): bool
	{
		return empty($this->nodes);
	}

	public function isExists(int $index): bool
	{
		return isset($this->nodes[$index]);
	}

	public function join(string $delimiter = self::DEFAULT_DELIMITER): string
	{
		return implode($delimiter, $this->nodes);
	}

	public function getCount(): int
	{
		return count($this->nodes);
	}

	public function getFirst(): ?string
	{
		return $this->nodes ? $this->nodes[0] : null;
	}

	public function getLast(): ?string
	{
		return $this->nodes ? $this->nodes[count($this->nodes) - 1] : null;
	}

	public function getIndexOf($value): ?int
	{
		$index = array_search($value, $this->nodes);
		return $index !== false ?: null;
	}

	public function removeFirst(): self
	{
		return $this->removeAt(0);
	}

	public function removeLast(): self
	{
		return $this->removeAt(count($this->nodes) - 1);
	}

	public function removeAt(int $index): self
	{
		unset($this->nodes[$index]);
		$this->nodes = array_values($this->nodes);
		return $this;
	}

	public function takeAt(int $index): ?string
	{
		$node = $this->nodes[$index] ?? null;
		$this->removeAt($index);
		return $node;
	}

	public function remove(string $value): self
	{
		$index = array_search($value, $this->nodes);
		if ($index !== false) {
			return $this->removeAt($index);
		}
		return $this;
	}

	public function removeAll(string $value): self
	{
		$nodes = [];
		foreach ($this->nodes as $node) {
			if ($node != $value) {
				$nodes[] = $node;
			}
		}
		$this->nodes = $nodes;
		return $this;
	}

	public function sub(int $offset, ?int $length = null): self
	{
		$instance = new static();
		$instance->nodes = array_slice($this->nodes, $offset, $length);
		return $instance;
	}

	protected static function normalize($path, string $delimiter = self::DEFAULT_DELIMITER, int $options = self::OPTIONS_DEFAULT): array
	{
		if ($path == false) {
			return [];
		}
		if ($path instanceof Path) {
			return $path->getAll();
		}
		if ($path instanceof \Traversable) {
			$path = iterator_to_array($path, false);
		}
		if (is_array($path) == false) {
			if (FilterKit::canBeString($path) == false) {
				return [];
			}
			if ($delimiter) {
				if ($options & self::OPTION_REGEX_DELIMITER) {
					$path = preg_split($delimiter, $path);
				} else {
					$path = explode($delimiter, $path);
				}
			} else {
				$path = [(string) $path];
			}
		}
		$result = [];
		foreach ($path as $node) {
			if (FilterKit::canBeString($node) == false) {
				continue;
			}
			$node = (string) $node;
			if ($options & self::OPTION_TRIM) {
				$node = trim($node);
			}
			if ($options & self::OPTION_CLEAN && $node == false) {
				continue;
			}
			if ($options & self::OPTION_LOWER_CASE) {
				$node = strtolower($node);
			}
			$result[] = $node;
		}
		return $result;
	}

	public function setPath($path, string $delimiter = self::DEFAULT_DELIMITER, int $options = self::OPTIONS_DEFAULT): self
	{
		$this->nodes = static::normalize($path, $delimiter, $options);
		return $this;
	}

	public function appendPath($path, string $delimiter = self::DEFAULT_DELIMITER, int $options = self::OPTIONS_DEFAULT): self
	{
		$this->nodes = array_merge($this->nodes, static::normalize($path, $delimiter, $options));
		return $this;
	}

	public function prependPath($path, string $delimiter = self::DEFAULT_DELIMITER, int $options = self::OPTIONS_DEFAULT): self
	{
		$this->nodes = array_merge(static::normalize($path, $delimiter, $options), $this->nodes);
		return $this;
	}

	/**
	 * Traverse through the specified source array and write the last found node to the value
	 * Returns true if the node was found, false otherwise
	 * TRAVERSE_WILDCARDS - use path nodes as wildcards when comparing against keys
	 * TRAVERSE_FALLBACK  - if the next node is not found, check if there is a node with the key '*'
	 */
	public function traverse(&$value, array $source, int $options = self::TRAVERSE_NONE): bool
	{
		$reference = $source;
		foreach ($this->nodes as $node) {
			if ($options & self::TRAVERSE_WILDCARDS) {
				if (is_array($reference)) {
					foreach ($reference as $key => $value) {
						if (fnmatch($node, $key)) {
							$reference = $value;
							continue(2);
						}
					}
				}
			} else {
				if (isset($reference[$node])) {
					$reference = $reference[$node];
					continue;
				}
			}
			if ($options & self::TRAVERSE_FALLBACK && isset($reference['*'])) {
				$value = $reference['*'];
				return true;
			}
			return false;
		}
		$value = $reference;
		return true;
	}

	/**
	 * Traverse through the specified source array and return the last found node
	 * @see Path::traverse
	 */
	public function read(array $source, $default = null, int $options = self::TRAVERSE_NONE)
	{
		$this->traverse($default, $source, $options);
		return $default;
	}

	/**
	 * Traverse through the specified source array and write the specified value to the last found node
	 * Returns the number of nodes written
	 * TRAVERSE_WILDCARDS - use path nodes as wildcards when comparing against keys
	 * TRAVERSE_COVERAGE  - when using wildcards, write the value to all matching nodes
	 */
	public function write(array &$destination, $value, int $options = self::TRAVERSE_NONE): int
	{
		$result = 0;
		$nodes = $this->nodes;
		reset($nodes);

		$write = function(&$destination) use (&$write, &$nodes, &$result, $value, $options) {
			$node = current($nodes);
			if ($node === false) {
				$destination = $value;
				++$result;
				return;
			}
			if (is_array($destination) == false) {
				return;
			}
			if (($options & self::TRAVERSE_WILDCARDS) == false) {
				next($nodes);
				$write($destination[$node]);
				return;
			}
			foreach ($destination as $key => &$item) {
				if (fnmatch($node, $key)) {
					next($nodes);
					$write($item);
					if (($options & self::TRAVERSE_COVERAGE) == false) {
						return;
					}
					if (key($nodes) === null) {
						end($nodes);
					} else {
						prev($nodes);
					}
				}
			}
		};

		$write($destination);
		return $result;
	}
}