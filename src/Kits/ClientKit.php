<?php

namespace Diz\Toolkit\Kits;

class ClientKit
{
	/**
	 * Resolve the user's IP address
	 */
	public static function resolveIP(): string
	{
		return $_SERVER['HTTP_X_REAL_IP'] ??
			(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
				ArrayKit::getLastElement(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) :
				$_SERVER['REMOTE_ADDR']
			);
	}
}