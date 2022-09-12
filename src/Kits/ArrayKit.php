<?php

namespace Diz\Toolkit\Kits;

class ArrayKit
{
	/**
	 * Checks whether the specified array is associative or not.
	 * Associative arrays:
	 * ['a' => 1, 'b' => 2, 'c' => 3]
	 * [2 => 'a', 4 => 'b', 0 => 'c']
	 * Non-associative arrays:
	 * [1, 2, 3]
	 * [0 => 'a', 1 => 'b', 2 => 'c']
	 * @param array $array The target array
	 */
	public static function isAssociative(array $array): bool
	{
		$keys = array_keys($array);
		return array_keys($keys) !== $keys;
	}

	/**
	 * Get the last element of the specified array
	 * @param array $array The target array
	 */
	public static function getLastElement(array $array)
	{
		return $array ? $array[array_key_last($array)] : null;
	}

	/**
	 * Get the first element of the specified array
	 * @param array $array The target array
	 */
	public static function getFirstElement(array $array)
	{
		return $array ? $array[array_key_first($array)] : null;
	}

	/**
	 * Get the string element of the specified array
	 * If there's no such element or, it can't be a string, null will be returned
	 * @param array $array The target array
	 * @param string|int $key The key for lookup
	 * @param string|null $default Default value to be returned on failure
	 */
	public static function getString(array $array, $key, ?string $default = null): ?string
	{
		$result = $array[$key] ?? null;
		return ($result !== null && FilterKit::canBeString($result)) ? $result : $default;
	}

	/**
	 * Get the trimmed string element of the specified array
	 * If there's no such element, it can't be a string, or it's string value does not contain useful data, null will be returned
	 * @param array $array The target array
	 * @param string|int $key The key for lookup
	 * @param string|null $default Default value to be returned on failure
	 */
	public static function getUsefulString(array $array, $key, ?string $default = null): ?string
	{
		$result = $array[$key] ?? null;
		if ($result && FilterKit::canBeString($result)) {
			return trim($result) ?: $default;
		}
		return $default;
	}

	/**
	 * Get the integer element of the specified array
	 * If there's no such element, or it can't be an integer, null will be returned
	 * @param array $array The target array
	 * @param string|int $key The key for lookup
	 * @param int|null $default Default value to be returned on failure
	 */
	public static function getInteger(array $array, $key, ?int $default = null, bool $strict = false): ?int
	{
		$result = $array[$key] ?? null;
		return $result !== null ? ($strict ? FilterKit::toInteger($result, $default) : FilterKit::toFloat($result, $default)) : $default;
	}

	/**
	 * Get the float element of the specified array
	 * If there's no such element, or it can't be a float, null will be returned
	 * @param array $array The target array
	 * @param string|int $key The key for lookup
	 * @param float|null $default Default value to be returned on failure
	 */
	public static function getFloat(array $array, $key, ?float $default = null): ?float
	{
		$result = $array[$key] ?? null;
		return $result !== null ? FilterKit::toFloat($result, $default) : $default;
	}

	/**
	 * Get the boolean element of the specified array
	 * If there's no such element, or it can't be a boolean, null will be returned
	 * @param array $array The target array
	 * @param string|int $key The key for lookup
	 * @param bool|null $default Default value to be returned on failure
	 */
	public static function getBoolean(array $array, $key, ?bool $default = null): ?bool
	{
		$result = $array[$key] ?? null;
		return $result !== null ? FilterKit::toBoolean($result, $default) : $default;
	}
}