<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\MathKit;

final class MathKitTest extends TestCase
{
	/**
	 * @covers MathKit::powerOfBase
	 */
	public function testPowerOfBase()
	{
		$this->assertSame([1.5, 2], MathKit::powerOfBase(384, 16));
		$this->assertSame([14.56, 5], MathKit::powerOfBase(15268788, 16, 2));
		$this->assertSame([14.561450958251953, 5], MathKit::powerOfBase(15268788, 16));
		$this->assertSame([0, 0], MathKit::powerOfBase(0, 16));
		$this->assertSame([16, 0], MathKit::powerOfBase(16, 0));
		$this->assertSame([-1.5, 2], MathKit::powerOfBase(-384, 16));
		$this->assertSame([384, 0], MathKit::powerOfBase(384, -16));
	}
}