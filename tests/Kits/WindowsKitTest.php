<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\WindowsKit;

final class WindowsKitTest extends TestCase
{
	/**
	 * @covers WindowsKit::escape
	 */
	public function testEscape(): void
	{
		$this->assertSame(
			['Aux_', 'lpt1_', 'com4_'],
			[WindowsKit::escape('Aux'), WindowsKit::escape('lpt1'), WindowsKit::escape('com4')],
			'Reserved words'
		);

		$test = 'foo\\bar*sub:<zero>?';

		$this->assertSame(
			'foo bar^sub {zero}!',
			WindowsKit::escape($test),
			'ASCII'
		);

		$this->assertSame(
			'foo⧵bar∗sub∶‹zero›？',
			WindowsKit::escape($test, [], true),
			'UTF8'
		);

		$this->assertSame(
			'foo bar+sub={zero}!',
			WindowsKit::escape($test, [
				'*' => '+',
				':' => '='
			]),
			'Custom pairs'
		);
	}

	/**
	 * @covers WindowsKit::isPathAbsolute
	 */
	public function testIsPathAbsolute(): void
	{
		$this->assertTrue(WindowsKit::isPathAbsolute('c:\foo\bar'));
		$this->assertTrue(WindowsKit::isPathAbsolute('  C:\foo\bar'));
		$this->assertTrue(WindowsKit::isPathAbsolute('\foo\bar'));
		$this->assertTrue(WindowsKit::isPathAbsolute('/foo/bar'));
		$this->assertFalse(WindowsKit::isPathAbsolute(':foo\bar'));
	}

	/**
	 * @covers PathKit::normalizePath
	 */
	public function testNormalizePath(): void
	{
		$this->assertSame('foo/bar/', WindowsKit::normalizePath('  foo/bar  '));
		$this->assertSame('c:/foo/bar/', WindowsKit::normalizePath('bar', ' c:/foo '));
		$this->assertSame('bar/', WindowsKit::normalizePath(' bar '));
		$this->assertSame('c:/bar/', WindowsKit::normalizePath(' c:/bar  ', 'd:/foo'));
		$this->assertSame('D:/foo/bar', WindowsKit::normalizePath('bar', 'D:/foo', false));
	}
}