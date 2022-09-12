<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\TextKit;

final class TextKitTest extends TestCase
{
	/**
	 * @covers TextKit::toCamelCase
	 */
	public function testToCamelCase(): void
	{
		$this->assertSame('dizTool-kit', TextKit::toCamelCase('Diz Tool-Kit'));
		$this->assertSame('fooBar', TextKit::toCamelCase('FOO_BAR'));
		$this->assertSame('subZero', TextKit::toCamelCase('__SUB   zero'));
		$this->assertSame('fooBarSub', TextKit::toCamelCase('FOO-BAR+sub', '-+'));
		$this->assertSame('rápidaÇoulaСлово', TextKit::toCamelCase('Rápida Çoula Слово'));
	}

	/**
	 * @covers TextKit::toSnakeCase
	 */
	public function testToSnakeCase(): void
	{
		$this->assertSame('diz_tool-kit+10__', TextKit::toSnakeCase('Diz Tool-Kit+10__'));
		$this->assertSame('-n_x_13_m7c', TextKit::toSnakeCase('-nX 13 M7C'));
		$this->assertSame('rápida+çoula++слово', TextKit::toSnakeCase('  Rápida  Çoula+ +Слово  ', '+'));
		$this->assertSame('xml_http_request_xa--_no', TextKit::toSnakeCase('XMLHttpRequestXA--No'));
		$this->assertSame('foo_bar', TextKit::toSnakeCase('FOO_Bar'));
	}

	/**
	 * @covers TextKit::toAcronym
	 */
	public function testAcronym(): void
	{
		$this->assertSame('DTK', TextKit::toAcronym('Diz Tool-Kit', true));
		$this->assertSame('FoO', TextKit::toAcronym('Full o.O'));
		$this->assertSame('BaR', TextKit::toAcronym('Bass -  and(ROCK!)'));
		$this->assertSame('N4S', TextKit::toAcronym('Need 4 Speed'));
		$this->assertSame('N13', TextKit::toAcronym('number-13', true));
		$this->assertSame('КГБ', TextKit::toAcronym('Комитет Государственной Безопасности'));
		$this->assertSame('кгб', TextKit::toAcronym('Комитет Государственной Безопасности', false));
	}

	/**
	 * @covers TextKit::capitalize
	 */
	public function testCapitalize(): void
	{
		$this->assertSame('Diz Tool-Kit', TextKit::capitalize('diz tool-kit'));
		$this->assertSame('Foo BAR 2020 ?Year!', TextKit::capitalize('foo BAR 2020 ?year!'));
		$this->assertSame('Sub (Zero~Null)', TextKit::capitalize('sub (zero~null)'));
		$this->assertSame('Rápida Çoula Слово', TextKit::capitalize('rápida çoula слово'));
	}

	/**
	 * @covers TextKit::isAlphanumeric
	 */
	public function testIsAlphanumeric(): void
	{
		$this->assertTrue(TextKit::isAlphanumeric('foo12bar'));
		$this->assertFalse(TextKit::isAlphanumeric('foo1_2bar'));
		$this->assertTrue(TextKit::isAlphanumeric('rápida2слово'));
		$this->assertFalse(TextKit::isAlphanumeric('rápida 2 слово'));
		$this->assertFalse(TextKit::isAlphanumeric('rápida слово'));
		$this->assertFalse(TextKit::isAlphanumeric(''));
	}

	/**
	 * @covers TextKit::isAlpha
	 */
	public function testIsAlpha(): void
	{
		$this->assertTrue(TextKit::isAlpha('fooBar'));
		$this->assertFalse(TextKit::isAlpha('foo1_2bar'));
		$this->assertFalse(TextKit::isAlpha('rápida2слово'));
		$this->assertTrue(TextKit::isAlpha('rápidaслово'));
		$this->assertFalse(TextKit::isAlpha(''));
	}

	/**
	 * @covers TextKit::isUpper
	 */
	public function testIsUpper(): void
	{
		$this->assertFalse(TextKit::isUpper('foo1_2bar'));
		$this->assertTrue(TextKit::isUpper('FOO1_2BAR'));
		$this->assertTrue(TextKit::isUpper('RÁPIDA ÇOULA СЛОВО'));
		$this->assertTrue(TextKit::isUpper(' '));
		$this->assertTrue(TextKit::isUpper(''));
		$this->assertTrue(TextKit::isUpper('=_='));
	}

	/**
	 * @covers TextKit::isLower
	 */
	public function testIsLower(): void
	{
		$this->assertTrue(TextKit::isLower('foo1_2bar'));
		$this->assertFalse(TextKit::isLower('FOO1_2BAR'));
		$this->assertTrue(TextKit::isLower('rápida çoula слово'));
		$this->assertTrue(TextKit::isLower(' '));
		$this->assertTrue(TextKit::isLower(''));
		$this->assertTrue(TextKit::isLower('=_='));
	}

	/**
	 * @covers TextKit::clarifyLower
	 */
	public function testClarifyLower(): void
	{
		$this->assertSame('foo bar', TextKit::clarifyLower(' foo Bar  '));
		$this->assertSame(null, TextKit::clarifyLower('  '));
		$this->assertSame(null, TextKit::clarifyLower(null));
	}

	/**
	 * @covers TextKit::clarifyUpper
	 */
	public function testClarifyUpper(): void
	{
		$this->assertSame('FOO BAR', TextKit::clarifyUpper(' FOO Bar  '));
		$this->assertSame(null, TextKit::clarifyUpper('  '));
		$this->assertSame(null, TextKit::clarifyUpper(null));
	}

	/**
	 * @covers TextKit::trimLower
	 */
	public function testTrimLower(): void
	{
		$this->assertSame('foo bar', TextKit::trimLower(' foo Bar  '));
		$this->assertSame('rápida', TextKit::trimLower(' RÁpida '));
		$this->assertSame('', TextKit::trimLower('  '));
	}

	/**
	 * @covers TextKit::trimUpper
	 */
	public function testTrimUpper(): void
	{
		$this->assertSame('FOO BAR', TextKit::trimUpper(' FOO Bar  '));
		$this->assertSame('RÁPIDA', TextKit::trimUpper(' rápiDA '));
		$this->assertSame('', TextKit::trimUpper('  '));
	}

	/**
	 * @covers TextKit::fit
	 */
	public function testFit(): void
	{
		$this->assertSame('rápida çoula слово', TextKit::fit('rápida çoula слово', 18));
		$this->assertSame('rápida çoula...', TextKit::fit('rápida çoula слово', 15));
		$this->assertSame('rápida--', TextKit::fit('rápida   çoula   слово', 10, '--'));
		$this->assertSame('rá...', TextKit::fit('rápida çoula слово', 5));
	}
}