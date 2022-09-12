<?php

namespace Diz\Toolkit\Kits;

use Diz\Toolkit\Iterators\SingleByteIterator;

class StringKit
{
	/**
	 * Convert the specified text to the camel case style.
	 * Spaces will also be treated as separators.
	 * @param string $text The target text
	 * @param string $separators One or more (string-packed) custom separator characters to break up text
	 * @return string Converted text
	 */
	public static function toCamelCase(string $text, string $separators = '_'): string
	{
		$result = strtok(static::toLower($text), $separators . ' ');
		while ($token = strtok($separators . ' ')) {
			$result .= static::toUpper(static::sub($token, 0, 1)) . static::sub($token, 1);
		}
		return $result;
	}

	/**
	 * Convert the specified text to the snake case style.
	 * Spaces will be removed, but will also be treated as the start of a word.
	 * @param string $text The target text
	 * @param string $separator The custom separator character for joining text
	 * @return string Converted text
	 */
	public static function toSnakeCase(string $text, string $separator = '_'): string
	{
		if (($text = trim($text)) == false) {
			return '';
		}
		$result = '';
		$predict = '';
		$acronym = 0;
		$solid = true;
		foreach (static::iterator($text) as $char) {
			if ($char == ' ') {
				$char = '';
				$acronym = 0;
				$solid = true;
			} else if ($char == $separator) {
				$acronym = 0;
				$solid = false;
			} else if (static::isAlpha($char) || $predict == '') {
				if (static::isUpper($char)) {
					$char = static::toLower($char);
					if ($acronym) {
						++$acronym;
					} else if ($solid) {
						if ($result) {
							$char = $separator . $char;
						}
						$acronym = 1;
					}
				} else {
					if ($acronym > 1) {
						$predict = $separator . $predict;
					}
					$acronym = 0;
				}
				$solid = true;
			} else {
				$solid = static::isAlphanumeric($char);
			}

			$result .= $predict;
			$predict = $char;
		}
		return $result . $predict;
	}

	/**
	 * Convert the specified text to an acronym ("alpha beta gamma" -> "abg")
	 * @param string $text The target text
	 * @param bool|null $letter_case If not null, then convert result to upper case on true and lower case on false
	 */
	public static function toAcronym(string $text, ?bool $letter_case = null): ?string
	{
		static $pattern = '._ -+=!@#$%^&*()~`"\'?<>,/\\;:';
		$result = static::sub(strtok($text, $pattern), 0, 1);
		while ($token = strtok($pattern)) {
			if (is_numeric($token)) {
				$result .= $token;
			} else {
				$result .= static::sub($token, 0, 1);
			}
		}
		if ($letter_case === null) {
			return $result;
		}
		return $letter_case ? static::toUpper($result) : static::toLower($result);
	}

	/**
	 * Capitalize the specified text using "smart" algorithm
	 * @param string $text The target text
	 */
	public static function capitalize(string $text): string
	{
		$result = '';

		$capitalize = true;
		$last_was_letter = false;
		$first = null;
		$chunk = null;

		$flush_chunk = function() use (&$capitalize, &$result, &$first, &$chunk)
		{
			if ($first == null) {
				return;
			}
			if ($capitalize) {
				$first = static::toUpper($first);
			}
			$result .= $first . $chunk;
			$first = null;
			$chunk = '';
		};

		foreach (static::iterator($text) as $char) {
			if (static::isAlphanumeric($char)) {
				$last_was_letter = true;
				if ($first == null) {
					if (is_numeric($char)) {
						$capitalize = false;
					}
					$first = $char;
				} else {
					$chunk .= $char;
				}
				if ($capitalize && static::isUpper($char)) {
					$capitalize = false;
				}
				continue;
			}

			$flush_chunk();
			$capitalize = ($char != '\'') || ($last_was_letter == false);
			$last_was_letter = false;
			$result .= $char;
		}

		$flush_chunk();
		return $result;
	}

	/**
	 * Check if text contains only alphanumeric characters
	 * @param string $text The target text
	 */
	public static function isAlphanumeric(string $text): bool
	{
		return $text && preg_match('/[^[:alnum:]]+/', $text) == 0;
	}

	/**
	 * Check if text contains only alphabetic characters
	 * @param string $text The target text
	 */
	public static function isAlpha(string $text): bool
	{
		return $text && preg_match('/[^[:alpha:]]+/', $text) == 0;
	}

	/**
	 * Check if text will be the same after converting to uppercase
	 * @param string $text The target text
	 */
	public static function isUpper(string $text): bool
	{
		return $text === static::toUpper($text);
	}

	/**
	 * Check if text will be the same after converting to lowercase
	 * @param string $text The target text
	 */
	public static function isLower(string $text): bool
	{
		return $text === static::toLower($text);
	}

	/**
	 * Trim the specified text and replace each sequence of internal whitespace, newlines and tabs with a single space
	 * @param string $text The target text
	 */
	public static function compact(string $text): string
	{
		return preg_replace('/(\s+|\n|\r)/', ' ', trim($text));
	}

	/**
	 * Trim the specified text, if it's empty - return null
	 * @param string|null $text The target text
	 */
	public static function clarify(?string $text): ?string
	{
		return $text ? (trim($text) ?: null) : null;
	}

	/**
	 * Trim and lowercase the specified text, if it's empty - return null
	 * @param string|null $text The target text
	 */
	public static function clarifyLower(?string $text): ?string
	{
		return $text ? (static::toLower(trim($text)) ?: null) : null;
	}

	/**
	 * Trim and uppercase the specified text, if it's empty - return null
	 * @param string|null $text The target text
	 */
	public static function clarifyUpper(?string $text): ?string
	{
		return $text ? (static::toUpper(trim($text)) ?: null) : null;
	}

	/**
	 * Trim and lowercase the specified text
	 * @param string $text The target text
	 */
	public static function trimLower(string $text): string
	{
		return static::toLower(trim($text));
	}

	/**
	 * Trim and uppercase the specified text
	 * @param string $text The target text
	 */
	public static function trimUpper(string $text): string
	{
		return static::toUpper(trim($text));
	}

	/**
	 * Check if text starts with a specified string
	 * @param string $text The target text
	 * @param string $string The string to match
	 * @param bool $case_sensitive Perform a case-sensitive comparison
	 */
	public static function startsWith(string $text, string $string, bool $case_sensitive = true): bool
	{
		if ($case_sensitive) {
			return (strcmp(substr($text, 0, strlen($string)), $string) === 0);
		}
		return (strcasecmp(substr($text, 0, strlen($string)), $string) === 0);
	}

	/**
	 * Check if text ends with a specified string
	 * @param string $text The target text
	 * @param string $string The string to match
	 * @param bool $case_sensitive Perform a case-sensitive comparison
	 */
	public static function endsWith(string $text, string $string, bool $case_sensitive = true): bool
	{
		if ($case_sensitive) {
			return (strcmp(substr($text, strlen($text) - strlen($string)), $string) === 0);
		}
		return (strcasecmp(substr($text, strlen($text) - strlen($string)), $string) === 0);
	}

	/**
	 * Replace the first occurrence of the search string with the replacement string in the specified text
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param string $replacement The replacement string
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function replaceFirst(string $text, string $search, string $replacement, bool $case_sensitive = true): string
	{
		$position = $case_sensitive ? strpos($text, $search) : stripos($text, $search);
		if ($position !== false) {
			return substr_replace($text, $replacement, $position, strlen($search));
		}
		return $text;
	}

	/**
	 * Replace the last occurrence of the search string with the replacement string in the specified text
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param string $replacement The replacement string
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function replaceLast(string $text, string $search, string $replacement, bool $case_sensitive = true): string
	{
		$position = $case_sensitive ? strrpos($text, $search) : strripos($text, $search);
		if ($position !== false) {
			return substr_replace($text, $replacement, $position, strlen($search));
		}
		return $text;
	}

	/**
	 * Frame all occurrences of the search string with the left and right string
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param string $left Left string part of the frame
	 * @param string|null $right Right string part of the frame, if not specified, will be the same as the left
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function frame(string $text, string $search, string $left, ?string $right = null, bool $case_sensitive = true): string
	{
		if ($right === null) {
			$right = $left;
		}
		$search_length = strlen($search);
		$insert_length = strlen($left) + strlen($right);
		$position = 0;
		while (true) {
			$position = $case_sensitive ? strpos($text, $search, $position) : stripos($text, $search, $position);
			if ($position === false) {
				return $text;
			}
			$text = substr_replace($text, $right, $position + $search_length, 0);
			$text = substr_replace($text, $left, $position, 0);
			$position += $insert_length;
		}
	}

	/**
	 * Frame the first occurrence of the search string with the left and right string
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param string $left Left string part of the frame
	 * @param string|null $right Right string part of the frame, if not specified, will be the same as the left
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function frameFirst(string $text, string $search, string $left, ?string $right = null, bool $case_sensitive = true): string
	{
		$position = $case_sensitive ? strpos($text, $search) : stripos($text, $search);
		if ($position !== false) {
			$text = substr_replace($text, $right === null ? $left : $right, $position + strlen($search), 0);
			return substr_replace($text, $left, $position, 0);
		}
		return $text;
	}

	/**
	 * Frame the first occurrence of the search string with the left and right string
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param string $left Left string part of the frame
	 * @param string|null $right Right string part of the frame, if not specified, will be the same as the left
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function frameLast(string $text, string $search, string $left, ?string $right = null, bool $case_sensitive = true): string
	{
		$position = $case_sensitive ? strrpos($text, $search) : strripos($text, $search);
		if ($position !== false) {
			$text = substr_replace($text, $right === null ? $left : $right, $position + strlen($search), 0);
			return substr_replace($text, $left, $position, 0);
		}
		return $text;
	}

	/**
	 * Remove the search string from the specified text if it starts with it
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function cleanLeft(string $text, string $search, bool $case_sensitive = true): string
	{
		if (static::startsWith($text, $search, $case_sensitive)) {
			return substr($text, strlen($search));
		}
		return $text;
	}

	/**
	 * Remove the search string from the specified text if it ends with it
	 * @param string $text The target text
	 * @param string $search The search string
	 * @param bool $case_sensitive Perform a case-sensitive search
	 */
	public static function cleanRight(string $text, string $search, bool $case_sensitive = true): string
	{
		if (static::endsWith($text, $search, $case_sensitive)) {
			return substr($text, 0, -strlen($search));
		}
		return $text;
	}

	/**
	 * Fit the text to the specified length with a suffix appended
	 * @param string $text The target text
	 * @param int $length The desired text length
	 * @param string $suffix The suffix to designate the truncated text
	 */
	public static function fit(string $text, int $length, string $suffix = '...'): string
	{
		if ($length <= 0) {
			return '';
		}

		$text = trim($text);
		if (static::length($text) <= $length) {
			return $text;
		}

		$length -= static::length($suffix);
		if ($length <= 0) {
			return '';
		}

		$result = [];
		$result_length = -1;
		foreach (preg_split('/\s/', $text) as $word) {
			if ($word == false) {
				continue;
			}
			$result_length += static::length($word) + 1;
			if ($result_length > $length) {
				if ($result == false) {
					return static::sub($word, 0, $length) . $suffix;
				}
				break;
			} else {
				$result[] = $word;
			}
		}
		return implode(' ', $result) . $suffix;
	}



	/**
	 * Convert the specified text to uppercase.
	 * Wrapping function for polymorphism support.
	 * @param string $text The target text
	 * @return string Converted text
	 */
	public static function toUpper(string $text): string
	{
		return strtoupper($text);
	}

	/**
	 * Convert the specified text to lowercase.
	 * Wrapping function for polymorphism support.
	 * @param string $text The target text
	 * @return string Converted text
	 */
	public static function toLower(string $text): string
	{
		return strtolower($text);
	}

	/**
	 * Get a part of the specified text.
	 * Wrapping function for polymorphism support.
	 * @param string $text The target text
	 * @param int $start Start position
	 * @param int|null $length The maximum length of the returned string.
	 * @return string Text part
	 */
	public static function sub(string $text, int $start, ?int $length = null): string
	{
		// PHP 7.4 treats a null length as zero
		return $length === null ? substr($text, $start) : substr($text, $start, $length);
		//return substr($text, $start, $length);
	}

	/**
	 * Get the length of the specified text.
	 * Wrapping function for polymorphism support.
	 * @param string $text The target text
	 * @return int|false Length
	 */
	#[\ReturnTypeWillChange]
	public static function length(string $text)
	{
		return strlen($text);
	}

	/**
	 * Create an instance of the iterator for the specified text.
	 * Wrapping function for polymorphism support.
	 * @param string $text The target text
	 * @return \Iterator The Iterator instance
	 */
	#[\ReturnTypeWillChange]
	public static function iterator(string $text): \Iterator
	{
		return new SingleByteIterator($text);
	}
}