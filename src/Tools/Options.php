<?php

namespace Diz\Toolkit\Tools;

use Diz\Toolkit\Kits\ArrayKit;

class Options implements \ArrayAccess
{
	private array $data;

	public function __construct(array $data = [])
	{
		$this->data = $data;
	}

	/**
	 * Get raw data
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Set raw data
	 * @param array $data
	 * @return static
	 */
	public function setData(array $data): self
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Get the item value for the specified key
	 * @param string|int $key The key for lookup
	 * @param mixed $default The default value
	 * @return mixed|null Item value or default value, if item does not exist
	 */
	public function get($key, $default = null)
	{
		return $this->data[$key] ?? $default;
	}

	/**
	 * Set the item value for the specified key
	 * @param string|int $key The key
	 * @param mixed $value The new value
	 * @return static
	 */
	public function set($key, $value): self
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Check if the item for the specified key exist and is equal to true
	 * @param string|int $key The key for lookup
	 * @return bool
	 */
	public function is($key): bool
	{
		return ($this->data[$key] ?? false) == true;
	}

	/**
	 * Check if the item for the specified key not exist or is equal to false
	 * @param string|int $key The key for lookup
	 * @return bool
	 */
	public function not($key): bool
	{
		return ($this->data[$key] ?? false) == false;
	}

	/**
	 * Check if the item exist for the specified key
	 * @param string|int $key The key for lookup
	 * @return bool
	 */
	public function has($key): bool
	{
		return isset($this->data[$key]);
	}

	/**
	 * Create a new Options instance for the sub array specified by the key
	 * @param string|int $key The key for lookup
	 * @return static|null An Options instance or null if the item is not found or is not an array
	 */
	public function branch($key): ?self
	{
		$data = $this->data[$key] ?? null;
		return is_array($data) ? new static($data) : null;
	}

	public function getBoolean($key, ?bool $default = null): ?bool
	{
		return ArrayKit::getBoolean($this->data, $key, $default);
	}

	public function getString($key, ?string $default = null): ?string
	{
		return ArrayKit::getString($this->data, $key, $default);
	}

	public function getUsefulString($key, ?string $default = null): ?string
	{
		return ArrayKit::getUsefulString($this->data, $key, $default);
	}

	public function getInteger($key, ?int $default = null): ?int
	{
		return ArrayKit::getInteger($this->data, $key, $default);
	}

	public function getFloat($key, ?float $default = null): ?float
	{
		return ArrayKit::getFloat($this->data, $key, $default);
	}

	public function offsetSet($offset, $value): void
	{
		$this->data[$offset] = $value;
	}

	public function offsetExists($offset): bool
	{
		return isset($this->data[$offset]);
	}

	public function offsetUnset($offset): void
	{
		unset($this->data[$offset]);
	}

	/**
	 * @return mixed|null
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->data[$offset] ?? null;
	}
}