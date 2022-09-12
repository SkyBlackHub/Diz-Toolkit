<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\StringKit;

final class StringKitTest extends TestCase
{
	/**
	 * @covers StringKit::toCamelCase
	 */
	public function testToCamelCase(): void
	{
		$this->assertSame('dizTool-kit', StringKit::toCamelCase('Diz Tool-Kit'));
		$this->assertSame('fooBar', StringKit::toCamelCase('FOO_BAR'));
		$this->assertSame('subZero', StringKit::toCamelCase('__SUB   zero'));
		$this->assertSame('fooBarSub', StringKit::toCamelCase('FOO-BAR+sub', '-+'));
	}

	/**
	 * @covers StringKit::toSnakeCase
	 */
	public function testToSnakeCase(): void
	{
		$this->assertSame('diz_tool-kit+10__', StringKit::toSnakeCase('Diz Tool-Kit+10__'));
		$this->assertSame('-n_x_13_m7c', StringKit::toSnakeCase('-nX 13 M7C'));
		$this->assertSame('xml_http_request_xa--_no', StringKit::toSnakeCase('XMLHttpRequestXA--No'));
		$this->assertSame('foo_bar', StringKit::toSnakeCase('FOO_Bar'));
	}

	/**
	 * @covers StringKit::toAcronym
	 */
	public function testAcronym(): void
	{
		$this->assertSame('DTK', StringKit::toAcronym('Diz Tool-Kit', true));
		$this->assertSame('FoO', StringKit::toAcronym('Full o.O'));
		$this->assertSame('BaR', StringKit::toAcronym('Bass -  and(ROCK!)'));
		$this->assertSame('N4S', StringKit::toAcronym('Need 4 Speed'));
		$this->assertSame('N13', StringKit::toAcronym('number-13', true));
	}

	/**
	 * @covers StringKit::capitalize
	 */
	public function testCapitalize(): void
	{
		$this->assertSame('Diz Tool-Kit', StringKit::capitalize('diz tool-kit'));
		$this->assertSame('Foo BAR 2020 ?Year!', StringKit::capitalize('foo BAR 2020 ?year!'));
		$this->assertSame('Sub (Zero~Null)', StringKit::capitalize('sub (zero~null)'));
	}

	/**
	 * @covers StringKit::isAlphanumeric
	 */
	public function testIsAlphanumeric(): void
	{
		$this->assertTrue(StringKit::isAlphanumeric('foo12bar'));
		$this->assertFalse(StringKit::isAlphanumeric('foo1_2bar'));
		$this->assertFalse(StringKit::isAlphanumeric(''));
	}

	/**
	 * @covers StringKit::isAlpha
	 */
	public function testIsAlpha(): void
	{
		$this->assertTrue(StringKit::isAlpha('fooBar'));
		$this->assertFalse(StringKit::isAlpha('foo1_2bar'));
		$this->assertFalse(StringKit::isAlpha(''));
	}

	/**
	 * @covers StringKit::isUpper
	 */
	public function testIsUpper(): void
	{
		$this->assertFalse(StringKit::isUpper('foo1_2bar'));
		$this->assertTrue(StringKit::isUpper('FOO1_2BAR'));
		$this->assertTrue(StringKit::isUpper(' '));
		$this->assertTrue(StringKit::isUpper(''));
		$this->assertTrue(StringKit::isUpper('=_='));
	}

	/**
	 * @covers StringKit::isLower
	 */
	public function testIsLower(): void
	{
		$this->assertTrue(StringKit::isLower('foo1_2bar'));
		$this->assertFalse(StringKit::isLower('FOO1_2BAR'));
		$this->assertTrue(StringKit::isLower(' '));
		$this->assertTrue(StringKit::isLower(''));
		$this->assertTrue(StringKit::isLower('=_='));
	}

	/**
	 * @covers StringKit::compact
	 */
	public function testCompact(): void
	{
		$this->assertSame('foo bar', StringKit::compact(' foo  bar  '));
		$this->assertSame('foo bar 123', StringKit::compact(' foo ' . "\n\n\n" . '  bar  ' . "\t\t" . ' 123'));
	}

	/**
	 * @covers StringKit::clarify
	 */
	public function testClarify(): void
	{
		$this->assertSame('foo Bar', StringKit::clarify(' foo Bar  '));
		$this->assertSame(null, StringKit::clarify('  '));
		$this->assertSame(null, StringKit::clarify(null));
	}

	/**
	 * @covers StringKit::clarifyLower
	 */
	public function testClarifyLower(): void
	{
		$this->assertSame('foo bar', StringKit::clarifyLower(' foo Bar  '));
		$this->assertSame(null, StringKit::clarifyLower('  '));
		$this->assertSame(null, StringKit::clarifyLower(null));
	}

	/**
	 * @covers StringKit::clarifyUpper
	 */
	public function testClarifyUpper(): void
	{
		$this->assertSame('FOO BAR', StringKit::clarifyUpper(' FOO Bar  '));
		$this->assertSame(null, StringKit::clarifyUpper('  '));
		$this->assertSame(null, StringKit::clarifyUpper(null));
	}

	/**
	 * @covers StringKit::trimLower
	 */
	public function testTrimLower(): void
	{
		$this->assertSame('foo bar', StringKit::trimLower(' foo Bar  '));
		$this->assertSame('', StringKit::trimLower('  '));
	}

	/**
	 * @covers StringKit::trimUpper
	 */
	public function testTrimUpper(): void
	{
		$this->assertSame('FOO BAR', StringKit::trimUpper(' FOO Bar  '));
		$this->assertSame('', StringKit::trimUpper('  '));
	}

	/**
	 * @covers StringKit::startsWith
	 */
	public function testStartsWith(): void
	{
		$this->assertTrue(StringKit::startsWith('Foo_Bar', 'Foo'));
		$this->assertFalse(StringKit::startsWith('foo_Bar', 'Foo'));
		$this->assertTrue(StringKit::startsWith('foo_Bar', 'Foo', false));
	}

	/**
	 * @covers StringKit::endsWith
	 */
	public function testEndsWith(): void
	{
		$this->assertTrue(StringKit::endsWith('Foo_Bar', 'Bar'));
		$this->assertFalse(StringKit::endsWith('Foo_bar', 'Bar'));
		$this->assertTrue(StringKit::endsWith('Foo_bar', 'Bar', false));
	}

	/**
	 * @covers StringKit::replaceFirst
	 */
	public function testReplaceFirst(): void
	{
		$this->assertSame('start 123 middle %var% end', StringKit::replaceFirst('start %var% middle %var% end', '%var%', '123'));
		$this->assertSame('start %Var% middle 123 end', StringKit::replaceFirst('start %Var% middle %var% end', '%var%', '123'));
		$this->assertSame('start 123 middle %var% end', StringKit::replaceFirst('start %Var% middle %var% end', '%var%', '123', false));
	}

	/**
	 * @covers StringKit::replaceLast
	 */
	public function testReplaceLast(): void
	{
		$this->assertSame('start %var% middle 123 end', StringKit::replaceLast('start %var% middle %var% end', '%var%', '123'));
		$this->assertSame('start 123 middle %Var% end', StringKit::replaceLast('start %var% middle %Var% end', '%var%', '123'));
		$this->assertSame('start %var% middle 123 end', StringKit::replaceLast('start %var% middle %Var% end', '%var%', '123', false));
	}

	/**
	 * @covers StringKit::frame
	 */
	public function testFrame(): void
	{
		$this->assertSame('start [token] middle [token] end', StringKit::frame('start token middle token end', 'token', '[', ']'));
		$this->assertSame('start [token] middle Token end', StringKit::frame('start token middle Token end', 'token', '[', ']'));
		$this->assertSame('start [token] middle [Token] end', StringKit::frame('start token middle Token end', 'token', '[', ']', false));
		$this->assertSame('start ---token--- middle ---token--- end', StringKit::frame('start token middle token end', 'token', '---'));
	}

	/**
	 * @covers StringKit::frameFirst
	 */
	public function testFrameFirst(): void
	{
		$this->assertSame('start [token] middle token end', StringKit::frameFirst('start token middle token end', 'token', '[', ']'));
		$this->assertSame('start Token middle [token] end', StringKit::frameFirst('start Token middle token end', 'token', '[', ']'));
		$this->assertSame('start [Token] middle token end', StringKit::frameFirst('start Token middle token end', 'token', '[', ']', false));
		$this->assertSame('start ---token--- middle token end', StringKit::frameFirst('start token middle token end', 'token', '---'));
	}

	/**
	 * @covers StringKit::frameLast
	 */
	public function testFrameLast(): void
	{
		$this->assertSame('start token middle [token] end', StringKit::frameLast('start token middle token end', 'token', '[', ']'));
		$this->assertSame('start [token] middle Token end', StringKit::frameLast('start token middle Token end', 'token', '[', ']'));
		$this->assertSame('start token middle [Token] end', StringKit::frameLast('start token middle Token end', 'token', '[', ']', false));
		$this->assertSame('start token middle ---token--- end', StringKit::frameLast('start token middle token end', 'token', '---'));
	}

	/**
	 * @covers StringKit::cleanLeft
	 */
	public function testCleanLeft(): void
	{
		$this->assertSame('_Bar_Foo_Bar', StringKit::cleanLeft('Foo_Bar_Foo_Bar', 'Foo'));
		$this->assertSame('foo_Bar', StringKit::cleanLeft('foo_Bar', 'Foo'));
		$this->assertSame('_Bar', StringKit::cleanLeft('foo_Bar', 'Foo', false));
	}

	/**
	 * @covers StringKit::cleanRight
	 */
	public function testCleanRight(): void
	{
		$this->assertSame('Foo_Bar_Foo_', StringKit::cleanRight('Foo_Bar_Foo_Bar', 'Bar'));
		$this->assertSame('Foo_bar', StringKit::cleanRight('Foo_bar', 'Bar'));
		$this->assertSame('Foo_', StringKit::cleanRight('Foo_bar', 'Bar', false));
	}
	
	/**
	 * @covers StringKit::fit
	 */
	public function testFit(): void
	{
		$this->assertSame('foo bar test', StringKit::fit('foo bar test', 12));
		$this->assertSame('foo bar...', StringKit::fit('foo bar test', 10));
		$this->assertSame('foo--', StringKit::fit('foo   bar   test', 5, '--'));
		$this->assertSame('fo...', StringKit::fit('foo bar test', 5));
	}
}