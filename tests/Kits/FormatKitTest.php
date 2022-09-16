<?php

namespace Diz\Toolkit\Tests\Kits;

use Diz\Toolkit\Kits\FormatKit;
use PHPUnit\Framework\TestCase;

class FormatKitTest extends TestCase
{
	/**
	 * @covers FormatKit::powerSizeUnits
	 */
	public function testPowerSizeUnits()
	{
		$this->assertSame('1.5 mh', FormatKit::powerSizeUnits(384, 16, ['h', 'kh', 'mh'], 1, ' '));
		$this->assertSame('mh: 1.50', FormatKit::powerSizeUnits(384, 16, ['h', 'kh', 'mh'], 2, '', '%3$s: %s'));
	}

	/**
	 * @covers FormatKit::powerSize
	 */
	public function testPowerSize()
	{
		$this->assertSame('1.50 x16^2', FormatKit::powerSize(384, 16, 2, ' '));
		$this->assertSame('14.56x16^5', FormatKit::powerSize(15268788, 16, 2));
		$this->assertSame('16-5-14.561', FormatKit::powerSize(15268788, 16, 3, '', '%4$s-%3$s-%s'));
	}

	/**
	 * @covers FormatKit::binarySize
	 */
	public function testBinarySize()
	{
		$this->assertSame('414 MiB', FormatKit::binarySize(433701329, 0, ' '));
		$this->assertSame('413.61 MiB', FormatKit::binarySize(433701329, 2, ' '));
	}

	/**
	 * @covers FormatKit::decimalSize
	 */
	public function testDecimalSize()
	{
		$this->assertSame('434 MB', FormatKit::decimalSize(433701329, 0, ' '));
		$this->assertSame('433.70 MB', FormatKit::decimalSize(433701329, 2, ' '));
	}
}
