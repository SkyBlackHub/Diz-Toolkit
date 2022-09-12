<?php

namespace Diz\Toolkit\Tests\Examples;

use Diz\Toolkit\Kits\PathKit;

class DotPathKit extends PathKit
{
	protected const DELIMITER = '.';

	public static function normalize(string $path, ?string $base_path = null, bool $trailing_dot = false): string
	{
		return parent::normalize($path, $base_path, $trailing_dot);
	}
}