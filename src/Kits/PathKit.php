<?php

namespace Diz\Toolkit\Kits;

class PathKit
{
	protected const DELIMITER = '/';

	/**
	 * Check if the specified path is absolute
	 * This is the same as ::hasLeadingDelimiter and just pass through to it
	 * @param string $path The target path
	 */
	public static function isAbsolute(string $path): bool
	{
		return static::hasLeadingDelimiter($path);
	}

	/**
	 * Normalize the path - trim and append a trailing delimiter
	 * @param string $path The target path
	 * @param string|null $base_path If the path is non-absolute, it will be converted using the specified base path
	 * @param bool $trailing_delimiter Append a trailing delimiter
	 */
	public static function normalize(string $path, ?string $base_path = null, bool $trailing_delimiter = true): string
	{
		$delimiter = preg_quote(static::DELIMITER);
		$path = preg_replace(['# *' . $delimiter . '+ *#', '#' . $delimiter . '{2,}#'], static::DELIMITER, $path);
		$path = static::removeTrailingDelimiter($path);
		$path = ltrim($path);
		if ($path == '') {
			return $base_path ? static::normalize($base_path, null, $trailing_delimiter) : '';
		}
		if ($trailing_delimiter) {
			$path .= static::DELIMITER;
		}
		if ($base_path == null || static::isAbsolute($path)) {
			return $path;
		}
		return static::normalize($base_path, null, true) . $path;
	}

	/**
	 * Check if the specified path has a leading delimiter
	 * @param string $path The target path
	 */
	public static function hasLeadingDelimiter(string $path): bool
	{
		return (ltrim($path)[0] ?? null) == static::DELIMITER;
	}

	/**
	 * Check if the specified path has a trailing delimiter
	 * @param string $path The target path
	 */
	public static function hasTrailingDelimiter(string $path): bool
	{
		return (rtrim($path)[-1] ?? null) == static::DELIMITER;
	}

	/**
	 * Check if the specified path has a leading delimiter and prepend it if it doesn't exist
	 * @param string $path The target path
	 */
	public static function adjustLeadingDelimiter(string $path): string
	{
		$path = ltrim($path);
		if (($path[0] ?? null) == static::DELIMITER) {
			return $path;
		}
		return static::DELIMITER . $path;
	}

	/**
	 * Check if the specified path has a trailing delimiter and append it if it doesn't exist
	 * @param string $path The target path
	 */
	public static function adjustTrailingDelimiter(string $path): string
	{
		$path = rtrim($path);
		if (($path[-1] ?? null) == static::DELIMITER) {
			return $path;
		}
		return $path . static::DELIMITER;
	}

	/**
	 * Remove all leading delimiters from the specified path
	 * @param string $path The target path
	 */
	public static function removeLeadingDelimiter(string $path): string
	{
		return ltrim($path, static::DELIMITER . ' ');
	}

	/**
	 * Remove all trailing delimiters from the specified path
	 * @param string $path The target path
	 */
	public static function removeTrailingDelimiter(string $path): string
	{
		return rtrim($path, static::DELIMITER . ' ');
	}

	/**
	 * Remove the last segment of the specified path
	 * @param string $path The target path
	 * @param bool $keep_delimiter Keep a trailing delimiter
	 */
	public static function removeLastSegment(string $path, bool $keep_delimiter = true): string
	{
		$position = strrpos($path, static::DELIMITER);
		if ($position === false) {
			return '';
		}
		if ($keep_delimiter) {
			$position += strlen(static::DELIMITER);
		}
		return substr($path, 0, $position);
	}

	/**
	 * Remove the first segment of the specified path
	 * @param string $path The target path
	 * @param bool $keep_delimiter Keep a leading delimiter
	 */
	public static function removeFirstSegment(string $path, bool $keep_delimiter = true): string
	{
		$position = strpos($path, static::DELIMITER);
		if ($position === false) {
			return '';
		}
		if ($keep_delimiter == false) {
			$position += strlen(static::DELIMITER);
		}
		return substr($path, $position);
	}
}