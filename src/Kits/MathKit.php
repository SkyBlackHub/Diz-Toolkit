<?php

namespace Diz\Toolkit\Kits;

class MathKit
{
	/**
	 * Represent integer value as a pair of a multiplier and a base with a natural exponent
	 * For example: (640, 16) -> [2.50, 2]; (1560000, 1000) -> [1.56, 2]
	 * @param int $value Target value
	 * @param int $base Base value
	 * @param int|null $round_precision If not null, round value with the specified precision
	 * @param int|null $max_exponent If not null, limit the maximum value of the exponent
	 * @return array{float, int} multiplier and exponent
	 */
	public static function powerOfBase(int $value, int $base, ?int $round_precision = null, ?int $max_exponent = null): array
	{
		if ($value < 0) {
			$value = abs($value);
			$negative = true;
		} else {
			$negative = false;
		}
		$base = max(0, $base);
		$exponent = (int) floor(($value ? log($value) : 0) / log($base));

		if ($max_exponent > 0) {
			$exponent = min($exponent, $max_exponent);
		}

		$value /= pow($base, $exponent);

		if ($negative) {
			$value *= -1;
		}

		return [$round_precision ? round($value, $round_precision) : $value, $exponent];
	}
}