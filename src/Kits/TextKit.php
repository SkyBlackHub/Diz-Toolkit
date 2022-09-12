<?php

namespace Diz\Toolkit\Kits;

use Diz\Toolkit\Iterators\MultiByteIterator;

class TextKit extends StringKit
{
	private const MB_ENCODING = 'UTF-8';

	/**
	 * @inheritDoc
	 */
	public static function isAlphanumeric(string $text): bool
	{
		return $text && preg_match('/[^[:alnum:]]+/u', $text) == 0;
	}

	/**
	 * @inheritDoc
	 */
	public static function isAlpha(string $text): bool
	{
		return $text && preg_match('/[^[:alpha:]]+/u', $text) == 0;
	}

	/**
	 * @inheritDoc
	 */
	public static function toUpper(string $text): string
	{
		return mb_strtoupper($text, static::MB_ENCODING);
	}

	/**
	 * @inheritDoc
	 */
	public static function toLower(string $text): string
	{
		return mb_strtolower($text, static::MB_ENCODING);
	}

	/**
	 * @inheritDoc
	 */
	public static function sub(string $text, int $start, ?int $length = null): string
	{
		return mb_substr($text, $start, $length, static::MB_ENCODING);
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public static function length(string $text)
	{
		return mb_strlen($text, static::MB_ENCODING);
	}

	/**
	 * @inheritDoc
	 */
	#[\ReturnTypeWillChange]
	public static function iterator(string $text): \Iterator
	{
		return new MultiByteIterator($text);
	}
}