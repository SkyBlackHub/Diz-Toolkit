<?php

namespace Diz\Toolkit\Kits;

class WindowsKit
{
	/**
	 * Try to adapt the specified text to the restrictions of the Windows file system
	 * @param string $text The target text
	 * @param array $pairs If character pairs specified, they will be merged with the default character replacement pairs
	 * @param bool $utf8 If enabled, restricted characters will be replaced with the closest analogues from the Unicode table
	 */
	public static function escape(string $text, array $pairs = [], bool $utf8 = false): string
	{
		return static::innerEscape($text, $pairs + ($utf8 ? static::$escape_base_utf_pairs : static::$escape_base_ascii_pairs));
	}

	/**
	 * Check if the specified path is absolute
	 * @param string $path The target path
	 */
	public static function isPathAbsolute(string $path): bool
	{
		$path = ltrim($path);
		return ($path[1] ?? null) == ':' || ($path[0] ?? null) == '\\' || ($path[0] ?? null) == '/';
	}

	/**
	 * Normalize the path - trim and append a trailing slash
	 * If the specified path is non-absolute, it will be converted using the specified base path
	 * @param string $path The target path
	 * @param string|null $base_path If the path is non-absolute, it will be converted using the specified base path
	 * @param bool $trailing_slash Append a trailing slash
	 */
	public static function normalizePath(string $path, ?string $base_path = null, bool $trailing_slash = true): string
	{
		$path = rtrim(trim($path), '/\\');
		if ($path == false) {
			return $base_path ? static::normalizePath($base_path, null, $trailing_slash) : '';
		}
		if ($trailing_slash) {
			$path .= '/';
		}
		if ($base_path == null || static::isPathAbsolute($path)) {
			return $path;
		}
		return static::normalizePath($base_path) . $path;
	}

	protected static array $escape_base_ascii_pairs = [
		'\\' => ' ',
		'/' => ' ',
		'<' => '{',
		'>' => '}',
		'|' => '-',
		'"' => '\'',
		':' => ' ',
		'?' => '!',
		'*' => '^'
	];

	protected static array $escape_base_utf_pairs = [
		'\\' => '⧵',
		'/' => '∕',
		'<' => '‹',
		'>' => '›',
		'|' => '∣',
		'"' => '″',
		':' => '∶',
		'?' => '？',
		'*' => '∗'
	];

	protected static array $escape_prohibited_names = [
		'aux', 'con', 'prn', 'nul',
		'lpt1', 'lpt2', 'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9',
		'com1', 'com2', 'com3', 'com4', 'com5', 'com6', 'com7', 'com8', 'com9'
	];

	/**
	 * @internal
	 */
	protected static function innerEscape(string $text, array $pairs): string
	{
		$text = strtr($text, $pairs);
		if (strlen($text) < 5 && in_array(strtolower($text), static::$escape_prohibited_names)) {
			return $text . '_';
		}
		return rtrim($text, ' .');
	}
}