<?php

namespace Diz\Toolkit\Kits;

class FilterKit
{
	/**
	 * Check if the passed value can be converted to a string
	 */
	public static function canBeString($value): bool
	{
		if (is_array($value)) {
			return false;
		}
		if (is_object($value)) {
			return method_exists($value, '__toString');
		}
		return true;
	}

	/**
	 * Check if the passed value can be safely converted to an integer
	 * @param mixed $value The target value
	 * @param bool $strict If true, then float values (1.2, '2.5', etc) will return false
	 */
	public static function canBeInteger($value, bool $strict = false): bool
	{
		if (is_int($value) || ($strict == false && is_float($value))) {
			return true;
		}
		return filter_var($value, $strict ? FILTER_VALIDATE_INT : FILTER_VALIDATE_FLOAT) !== false;
	}

	/**
	 * Check if the passed value can be safely converted to a float
	 * @param mixed $value The target value
	 */
	public static function canBeFloat($value): bool
	{
		if (is_float($value)) {
			return true;
		}
		return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
	}

	/**
	 * Convert the passed value to an integer, return the default value on failure
	 * Pay attention! This is a strict method, it will return null for float values (1.2, '2.5', etc)
	 * Use ::toFloat instead if you expect a float input
	 * @param mixed $value The target value
	 * @param int|null $min_range Allowed minimum value
	 * @param int|null $max_range Allowed maximum value
	 * @param int|null $default The default value
	 */
	public static function toInteger($value, ?int $default = null, ?int $min_range = null, ?int $max_range = null): ?int
	{
		$options = [];
		if ($min_range !== null) {
			$options['min_range'] = $min_range;
		}
		if ($max_range !== null) {
			$options['max_range'] = $max_range;
		}
		if ($options == false && is_int($value)) {
			return $value;
		}
		if ($default !== null) {
			$options['default'] = $default;
		}
		return filter_var($value, FILTER_VALIDATE_INT, [
			'options' => $options,
			'flags' => FILTER_NULL_ON_FAILURE | FILTER_FLAG_ALLOW_HEX
		]);
	}

	/**
	 * Convert the passed value to an unsigned integer, return the default value on failure
	 * Pay attention! This is a strict method, it will return null for float and negative values (1.2, '2.5', -7, etc)
	 * Use ::toFloat instead if you expect a float input
	 * @param mixed $value The target value
	 * @param int|null $min_range Allowed minimum value
	 * @param int|null $max_range Allowed maximum value
	 * @param int|null $default The default value
	 */
	public static function toUnsignedInteger($value, ?int $default = null, ?int $min_range = null, ?int $max_range = null): ?int
	{
		$result = static::toInteger($value, $default, $min_range, $max_range);
		return $result >= 0 ? $result : null;
	}

	/**
	 * Convert the passed value to a float, return the default value on failure
	 * @param mixed $value The target value
	 * @param float|null $min_range Allowed minimum value
	 * @param float|null $max_range Allowed maximum value
	 * @param float|null $default The default value
	 */
	public static function toFloat($value, ?float $default = null, ?float $min_range = null, ?float $max_range = null): ?float
	{
		$options = [];
		if ($min_range !== null) {
			$options['min_range'] = $min_range;
		}
		if ($max_range !== null) {
			$options['max_range'] = $max_range;
		}
		if ($options == false && is_float($value)) {
			return $value;
		}
		if ($default !== null) {
			$options['default'] = $default;
		}
		return filter_var($value, FILTER_VALIDATE_FLOAT, [
			'options' => $options,
			'flags' => FILTER_NULL_ON_FAILURE
		]);
	}

	/**
	 * Convert the passed value to a boolean, return the default value on failure
	 * @param mixed $value The target value
	 * @param bool|null $default The default value
	 */
	public static function toBoolean($value, ?bool $default = null): ?bool
	{
		$options = [];
		if ($default !== null) {
			$options['default'] = $default;
		}
		return filter_var($value, FILTER_VALIDATE_BOOLEAN, [
			'options' => $options,
			'flags' => FILTER_NULL_ON_FAILURE
		]);
	}

	/**
	 * Convert the passed value to a DateTime object, return the default value on failure
	 * @param mixed $value The target value
	 * @param \DateTimeZone|null $timezone Time zone for a DateTime instance
	 * @param \DateTime|null $default The default value
	 */
	public static function toDateTime($value, ?\DateTimeZone $timezone = null, ?\DateTime $default = null): ?\DateTime
	{
		if ($value instanceof \DateTime) {
			return $value;
		}
		if (static::canBeInteger($value, true)) {
			$value = '@' . $value;
		}
		if (is_string($value)) {
			try {
				$value = new \DateTime($value);
			} catch (\Exception $exception) {
				return $default;
			}
			if ($timezone) {
				$value->setTimezone($timezone);
			}
			return $value;
		}
		return $default;
	}

	/**
	 * Convert the passed array to an array of integers
	 * Pay attention! This is a strict method, it will discard float values (1.2, '2.5', etc)
	 * Use ::toFloatArray instead if you expect a float input
	 * @param array $data The target data array
	 * @param bool $unique Filter result for unique values
	 * @param int|null $min_range Allowed minimum value
	 * @param int|null $max_range Allowed maximum value
	 */
	public static function toIntegerArray(array $data, bool $unique = false, ?int $min_range = null, ?int $max_range = null): array
	{
		$result = array_filter(array_map(function($value) use ($min_range, $max_range) {
			return static::toInteger($value, null, $min_range, $max_range);
		}, $data), function($value) {
			return $value !== null;
		});
		if ($unique) {
			$result = array_unique($result, SORT_REGULAR);
		}
		return array_values($result);
	}

	/**
	 * Convert the passed array to an array of floats
	 * @param array $data The target data array
	 * @param bool $unique Filter result for unique values
	 * @param float|null $min_range Allowed minimum value
	 * @param float|null $max_range Allowed maximum value
	 */
	public static function toFloatArray(array $data, bool $unique = false, ?float $min_range = null, ?float $max_range = null): array
	{
		$result = array_filter(array_map(function($value) use ($min_range, $max_range) {
			return static::toFloat($value, null, $min_range, $max_range);
		}, $data), function($value) {
			return $value !== null;
		});
		if ($unique) {
			$result = array_unique($result, SORT_REGULAR);
		}
		return array_values($result);
	}

	/**
	 * Convert the passed array to an array of trimmed strings
	 * Values can be filtered by uniqueness and truncated by length
	 * @param array $data The target data array
	 * @param bool $unique Filter result for unique values
	 * @param int|null $length If not null, trim all strings over this length
	 */
	public static function toTrimmedArray(array $data, bool $unique = false, ?int $length = null): array
	{
		return static::filterStringArray($data, 'trim', $unique, $length);
	}

	/**
	 * Convert the passed array to an array of trimmed uppercase strings
	 * Values can be filtered by uniqueness and truncated by length
	 * @param array $data The target data array
	 * @param bool $unique Filter result for unique values
	 * @param int|null $length If not null, trim all strings over this length
	 */
	public static function toUpperCaseArray(array $data, bool $unique = false, ?int $length = null): array
	{
		return static::filterStringArray($data, function($value) {
			return strtoupper(trim($value));
		}, $unique, $length);
	}

	/**
	 * Convert the passed array to an array of trimmed lowercase strings
	 * Values can be filtered by uniqueness and truncated by length
	 * @param array $data The target data array
	 * @param bool $unique Filter result for unique values
	 * @param int|null $length If not null, trim all strings over this length
	 */
	public static function toLowerCaseArray(array $data, bool $unique = false, ?int $length = null): array
	{
		return static::filterStringArray($data, function($value) {
			return strtolower(trim($value));
		}, $unique, $length);
	}

	/**
	 * @internal
	 */
	protected static function filterStringArray(array $data, ?callable $callback = null, bool $unique = false, ?int $length = null): array
	{
		if ($length !== null) {
			$result = array_map(function($value) use ($callback, $length) {
				return static::canBeString($value) ? substr($callback($value), 0, $length) : null;
			}, $data);
		} else {
			$result = array_map(function($value) use ($callback) {
				return static::canBeString($value) ? $callback($value) : null;
			}, $data);
		}
		$result = array_filter($result);
		if ($unique) {
			$result = array_unique($result, SORT_REGULAR);
		}
		return array_values($result);
	}
}