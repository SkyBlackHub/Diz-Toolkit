<?php

namespace Diz\Toolkit\Kits;

use Diz\Toolkit\Tools\Options;

class URLKit
{
	public const FALLBACK_HOST   = 'localhost';
	public const FALLBACK_SCHEME = 'https';

	// numeric_indices_mode - Keep or drop indices for nested linear arrays in the query
	public const QUERY_DROP_NUMERIC_INDICES = 0; // foo[1] -> foo[]
	public const QUERY_KEEP_NUMERIC_INDICES = 1; // foo[1] -> foo[1]
	public const QUERY_FLAT_NUMERIC_INDICES = 2; // foo[1] -> foo

	/**
	 * Compose a URL from the specified parts
	 * @param array $parts Component parts of the URL in a format similar to the parse_url function
	 * @param array{
	 *   numeric_indices_mode?: int|null
	 * } $options Optional settings
	 */
	public static function compose(array $parts, array $options = []): string
	{
		$parts = new Options($parts);

		$result = $parts->getUsefulString('scheme', static::FALLBACK_SCHEME) . '://';

		if ($user = $parts->getUsefulString('user')) {
			if ($pass = $parts->getUsefulString('pass')) {
				$result .= $user . ':' . $pass . '@';
			} else {
				$result .= $user . '@';
			}
		}

		$result .= $parts->getUsefulString('host', static::FALLBACK_HOST);

		if ($port = $parts->getUsefulString('port')) {
			$result .= ':' . $port;
		}

		$result .= '/';

		if ($path = $parts->getUsefulString('path')) {
			$result .= ltrim($path, '/');
		}

		if ($query = $parts->get('query')) {
			if (is_array($query)) {
				$result .= '?' . static::buildQuery($query, null, $options['numeric_indices_mode'] ?? null);
			} else if (FilterKit::canBeString($query)) {
				if ($query = trim($query)) {
					$result .= '?' . $query;
				}
			}
		}

		if ($fragment = $parts->getUsefulString('fragment')) {
			$result .= '#' . $fragment;
		}

		return $result;
	}

	/**
	 * Extend the specified base URL with one or more URLs
	 * @param string|array $base Base URL or component parts
	 * @param string|array ...$parts Extending URL or component parts
	 */
	public static function extend($base, ...$parts): ?string
	{
		if (is_array($base) == false) {
			$base = FilterKit::canBeString($base) ? parse_url($base) : [];
		}
		foreach ($parts as $part) {
			if (is_array($part) == false) {
				$part = FilterKit::canBeString($part) ? parse_url($part) : [];
			}
			$base += $part;
		}
		return static::compose($base);
	}

	/**
	 * Get the base path of the specified path
	 * @param string $path
	 */
	public static function basepath(string $path): string
	{
		$position = strrpos($path, '/');
		return $position === false ? '' : substr($path, 0, $position + 1);
	}

	/**
	 * Creates an absolute URL from the specified target using the base source for scheme, host, port and path if they are not set
	 * @param string|array $target Target URL or component parts
	 * @param string|array $base Base URL or component parts
	 */
	public static function complete($target, $base): string
	{
		if (is_array($target) == false) {
			$target = FilterKit::canBeString($target) ? parse_url($target) : [];
		}
		if (is_array($base) == false) {
			$base = FilterKit::canBeString($base) ? parse_url($base) : [];
		}
		if (isset($target['scheme']) == false) {
			$target['scheme'] = $base['scheme'] ?? static::FALLBACK_SCHEME;
		}
		if (isset($target['host']) == false) {
			$target['host'] = $base['host'] ?? '';
			if (isset($target['port']) == false && ($port = $base['port'] ?? null)) {
				$target['port'] = $port;
			}
			if ($path = $target['path'] ?? null) {
				if ($path[0] != '/' && $base_path = $base['path'] ?? null) {
					$target['path'] = static::basepath($base_path) . $path;
				}
			} else {
				$target['path'] = static::basepath($base['path'] ?? '');
			}
		}
		return static::compose($target);
	}

	/**
	 * Encode the specified URL
	 * This function encodes only characters that are not allowed for URLs, leaving special characters untouched
	 * @param string $url The target URL
	 */
	public static function encode(string $url): string
	{
		static $chars = '-._~:/?#[]@!$&\\\'() * +,;=';
		$pattern = '~[^a-z0-9' . preg_quote($chars, '~') . ']+~iu';
		return preg_replace_callback($pattern, function ($match) {
			return urlencode($match[0]);
		}, $url);
	}

	public const DEFAULT_QUERY_NUMERIC_INDICES_MODE = self::QUERY_DROP_NUMERIC_INDICES;

	/**
	 * Build an HTTP query from the specified data
	 * This function is similar to http_build_query, but it doesn't encode everything, and optionally doesn't add indices for linear arrays
	 * @param array $data The target data
	 * @param string|null $parent The parent key
	 * @param int|null $numeric_indices_mode Keep or drop indices for nested linear arrays (default: drop)
	 */
	public static function buildQuery(array $data, ?string $parent = null, ?int $numeric_indices_mode = null): string
	{
		$result = [];
		foreach ($data as $key => $value) {
			if ($parent) {
				if (is_integer($key)) {
					switch ($numeric_indices_mode ?? static::DEFAULT_QUERY_NUMERIC_INDICES_MODE) {
						case static::QUERY_DROP_NUMERIC_INDICES:
							$key = "{$parent}[]";
							break;

						case static::QUERY_KEEP_NUMERIC_INDICES:
							$key = "{$parent}[{$key}]";
							break;

						case static::QUERY_FLAT_NUMERIC_INDICES:
							$key = $parent;
							break;
					}
				} else {
					$key = urlencode($key);
					$key = "{$parent}[{$key}]";
				}
			} else {
				$key = urlencode($key);
			}
			if (is_array($value)) {
				$result[] = static::buildQuery($value, $key, $numeric_indices_mode);
			} else {
				$result[] = $key . '=' . urlencode($value);
			}
		}
		return implode('&', $result);
	}
}