<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\ClientKit;

final class ClientKitTest extends TestCase
{
	/**
	 * @covers ClientKit::resolveIP
	 * @testdox Resolve User's IP Address
	 */
	public function testResolveIP(): void
	{
		$ip = '1.2.3.4';
		foreach (['HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
			$server = $_SERVER;
			if ($key == 'HTTP_X_FORWARDED_FOR') {
				$_SERVER[$key] = '192.168.1.1,' . $ip;
			} else {
				$_SERVER[$key] = $ip;
			}
			$this->assertSame($ip, ClientKit::resolveIP(), $key);
			$_SERVER = $server;
		}
	}
}