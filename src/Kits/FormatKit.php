<?php

namespace Diz\Toolkit\Kits;

class FormatKit
{
	public const DECIMAL_UNITS = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	public const BINARY_UNITS  = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

	public const POWER_SIZE_PATTERN = '%s%sx%4$d^%3$d';

	/**
	 * Format the integer value as a multiple of the specified base with natural exponent
	 * and replace the exponent with a corresponding unit
	 * For example: (640, 16, [a, b, c]) -> 2.50c; (1560000, 1000, [B, kB, MB]) -> 1.56MB
	 * @param int $value Target value
	 * @param int $base Base value
	 * @param array $units Array of units
	 * @param int $precision Number of decimal places
	 * @param string $separator String separator between value and unit
	 * @param string|null $pattern Optional pattern for sprintf with arguments (value, separator, unit, exponent, base)
	 * @return string Formatted text
	 */
	public static function powerSizeUnits(int $value, int $base, array $units, int $precision = 2, string $separator = '', ?string $pattern = null): string
	{
		list($value, $exponent) = MathKit::powerOfBase($value, $base, $precision, count($units) - 1);
		$value = number_format($value, $precision, '.', ' ');
		$unit = $units[$exponent] ?? '';
		return $pattern !== null ? sprintf($pattern, $value, $separator, $unit, $exponent, $base) : $value . $separator . $unit;
	}

	/**
	 * Format the integer value as a multiple of the specified base with natural exponent
	 * For example: (640, 16) -> 2.50x16^2; (1560000, 1000) -> 1.56x1000^2
	 * @param int $value Target value
	 * @param int $base Base value
	 * @param int $precision Number of decimal places
	 * @param string $separator String separator between value and unit
	 * @param string|null $pattern Optional pattern for sprintf with arguments (value, separator, exponent, base)
	 * @param int|null $max_exponent If not null, limit the maximum value of the exponent
	 * @return string Formatted text
	 */
	public static function powerSize(int $value, int $base, int $precision = 2, string $separator = '', ?string $pattern = null, ?int $max_exponent = null): string
	{
		if ($pattern === null) {
			$pattern = static::POWER_SIZE_PATTERN;
		}
		list($value, $exponent) = MathKit::powerOfBase($value, $base, $precision, $max_exponent);
		$value = number_format($value, $precision, '.', ' ');
		return sprintf($pattern, $value, $separator, $exponent, $base);
	}

	/**
	 * Format the size in bytes as a multiple of the 1024^x data units
	 * @param int $value Target value
	 * @param int $precision Number of decimal places
	 * @param string $separator String separator between value and unit
	 * @return string Formatted text
	 */
	public static function binarySize(int $value, int $precision = 2, string $separator = ''): string
	{
		return static::powerSizeUnits($value, 1024, static::BINARY_UNITS, $precision, $separator);
	}

	/**
	 * Format the size in bytes as a multiple of the 1000^x data units
	 * @param int $value Target value
	 * @param int $precision Number of decimal places
	 * @param string $separator String separator between value and unit
	 * @return string Formatted text
	 */
	public static function decimalSize(int $value, int $precision = 2, string $separator = ''): string
	{
		return static::powerSizeUnits($value, 1000, static::DECIMAL_UNITS, $precision, $separator);
	}
}