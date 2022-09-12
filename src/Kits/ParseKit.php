<?php

namespace Diz\Toolkit\Kits;

use Diz\Toolkit\Iterators\DiapasonIterator;
use Diz\Toolkit\Iterators\TraversalIterator;

class ParseKit
{
	/**
	 * Convert data to a series of integer values and/or diapasons
	 * @param mixed $data The target data
	 * @param bool $mixed Is set, non-integer values will be added to the result too
	 */
	public static function toSeries($data, bool $mixed = false): ?TraversalIterator
	{
		if (FilterKit::canBeInteger($data)) {
			return new TraversalIterator([$mixed ? $data : intval($data)]);
		}
		if (is_string($data)) {
			$data = array_filter(array_map('trim', explode(',', $data)));
		}
		if (is_array($data) || $data instanceof \Traversable) {
			$series = [];
			foreach ($data as $value) {
				if (FilterKit::canBeInteger($value)) {
					$series[] = intval($value);
					continue;
				}
				$diapason = static::toDiapason($value);
				if ($diapason) {
					$series[] = $diapason;
					continue;
				}
				if ($mixed) {
					$series[] = $value;
				}
			}
			return new TraversalIterator($series);
		}
		if ($mixed) {
			return new TraversalIterator([$data]);
		}
		return null;
	}

	/**
	 * Convert data to a diapason
	 * String pattern: start-[end]:[step]
	 * @param mixed $data The target data
	 */
	public static function toDiapason($data): ?DiapasonIterator
	{
		if ($data instanceof DiapasonIterator) {
			return $data;
		}
		if (FilterKit::canBeInteger($data)) {
			return new DiapasonIterator($data, $data);
		}
		if (is_string($data)) {
			$pattern = '/^ *(-? *\d+) *(?:([-+]) *(-? *\d+) *(?:: *(\d+))? *)*$/m';
			if (preg_match($pattern, $data, $matches)) {
				$start = intval(str_replace(' ', '', $matches[1]));
				$end = isset($matches[3]) ? intval(str_replace(' ', '', $matches[3])) : $start;
				if (($matches[2] ?? null) == '+') {
					$end += $start;
				}
				$step = isset($matches[4]) ? intval($matches[4]) : 1;
				return new DiapasonIterator($start, $end, $step);
			}
			return null;
		}
		if (is_array($data)) {
			$start = $data['start'] ?? $data[0] ?? null;
			$end = $data['end'] ?? $data[1] ?? null;
			$step = $data['step'] ?? $data[2] ?? 1;
			if ($start === null || FilterKit::canBeInteger($start) == false) {
				return null;
			}
			if ($end === null || FilterKit::canBeInteger($end) == false) {
				$end = $start;
			}
			if (FilterKit::canBeInteger($step) == false) {
				$step = 1;
			}
			return new DiapasonIterator($start, $end, $step);
		}
		return null;
	}

	/**
	 * Convert data to an array of numbers (integers and/or floats)
	 * @param mixed $data The target data
	 * @param string|null $separator
	 * @param bool $unique
	 * @param int|null $min_range
	 * @param int|null $max_range
	 * @return array
	 */
	public static function toNumberArray($data, ?string $separator = null, bool $unique = false, ?int $min_range = null, ?int $max_range = null): array
	{
		return static::toLinearArray($data, $separator, $unique, function($value) use ($min_range, $max_range) {
			$value = FilterKit::toFloat($value, $min_range, $max_range);
			return $value !== null ? (fmod($value, 1) ? $value : (int) $value) : null;
		});
	}

	/**
	 * Convert data to an array of size pairs [[(int) width, (int) height]]
	 * Default string pattern: wxh, wxh, ...
	 * @param mixed $data The target data
	 * @param bool $unique Filter result for unique values
	 * @param int|null $min_range Allowed minimum value
	 * @param int|null $max_range Allowed maximum value
	 * @param array $separators The list of custom separators
	 */
	public static function toSizes($data, bool $unique = false, ?int $min_range = null, ?int $max_range = null, array $separators = [',', 'x']): array
	{
		$result = static::toNestedArray($data, $separators, function($value, string $separator, int $level) use ($min_range, $max_range) {
			if ($level == 0) {
				return FilterKit::toInteger($value, null, $min_range, $max_range);
			}
			if ($level == 1 && is_array($value) == false || count($value) != 2) {
				return null;
			}
			return $value;
		});
		if ($unique) {
			$result = array_values(array_unique($result, SORT_REGULAR));
		}
		return $result;
	}

	/**
	 * Convert data to a one dimensional array, breaking the input string into parts using the separator, and calling a callback for each element
	 * @param mixed $data The target data
	 * @param string|null $separator Breakdown separator
	 * @param callable|null $callback Callback for each item: mixed (mixed $value)
	 */
	public static function toLinearArray($data, ?string $separator = null, bool $unique = false, ?callable $callback = null): array
	{
		if ($data === null) {
			return [];
		}
		if ($separator !== null && is_string($data)) {
			$data = explode($separator, $data);
		}
		if (is_array($data) == false) {
			$data = [$data];
		}
		if ($callback !== null) {
			$data = array_filter(array_map($callback, $data), function($value) {
				return $value !== null;
			});
		}
		if ($unique) {
			$data = array_unique($data, SORT_REGULAR);
		}
		return array_values($data);
	}

	/**
	 * Convert data to a multidimensional array, breaking the input strings into parts using the list of separators, and calling a callback for each element before and after breakdown
	 * Pay attention! The level in the callbacks is the opposite, i.e. "level = 0" stand for the last element in the tree, and the upper is "level = count(separators) - 1"
	 * @param mixed $data The target data
	 * @param array $separators The list of breakdown separators
	 * @param callable|null $after_callback Callback for each item after breakdown: mixed (mixed $value, string separator, int level)
	 * @param callable|null $before_callback Callback for each item before breakdown: mixed (mixed $value, string separator, int level)
	 */
	public static function toNestedArray($data, array $separators, ?callable $after_callback = null, ?callable $before_callback = null): array
	{
		if ($separators == false) {
			return [];
		}
		$separator = array_shift($separators);
		if (FilterKit::canBeString($separator) == false) {
			return [];
		}
		return static::toLinearArray($data, $separator, false, function($value) use ($separators, $separator, $after_callback, $before_callback) {
			$level = count($separators);
			if ($before_callback) {
				$value = $before_callback($value, $separator, $level);
			}
			if ($level > 0) {
				$value = static::toNestedArray($value, $separators, $after_callback);
			}
			if ($after_callback) {
				$value = $after_callback($value, $separator, $level);
			}
			return $value;
		});
	}
}