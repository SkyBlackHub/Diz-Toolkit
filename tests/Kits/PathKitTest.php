<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\PathKit;
use Diz\Toolkit\Tests\Examples\DotPathKit;

final class PathKitTest extends TestCase
{
	/**
	 * @covers PathKit::isAbsolute
	 */
	public function testIsAbsolute(): void
	{
		$this->assertTrue(PathKit::isAbsolute('/foo/bar'));
		$this->assertTrue(PathKit::isAbsolute('   /foo/bar'));
		$this->assertFalse(PathKit::isAbsolute('foo/bar/'));
		$this->assertFalse(PathKit::isAbsolute('\foo\bar'));
	}

	/**
	 * @covers PathKit::normalize
	 */
	public function testNormalize(): void
	{
		$this->assertSame('foo/bar/', PathKit::normalize('  foo/bar  '));
		$this->assertSame('/foo/bar/', PathKit::normalize('bar', ' /foo '));
		$this->assertSame('bar/', PathKit::normalize(' bar '));
		$this->assertSame('/foo/bar', PathKit::normalize('bar', '/foo', false));
		$this->assertSame('/foo/bar/', PathKit::normalize('bar   /', ' /foo '));
		$this->assertSame('/foo/bar', PathKit::normalize(' // foo/// bar   /', null, false));
		$this->assertSame('', PathKit::normalize(' ////  /// /', null, false));
		$this->assertSame('/foo   bar/', PathKit::normalize(' / foo   bar  '));
	}

	/**
	 * @covers PathKit::hasLeadingDelimiter
	 */
	public function testHasLeadingDelimiter(): void
	{
		$this->assertTrue(PathKit::hasLeadingDelimiter('/foo/bar'));
		$this->assertTrue(PathKit::hasLeadingDelimiter('   /foo/bar'));
		$this->assertFalse(PathKit::hasLeadingDelimiter('foo/bar'));
		$this->assertFalse(PathKit::hasLeadingDelimiter('\foo\bar'));
	}

	/**
	 * @covers PathKit::hasTrailingDelimiter
	 */
	public function testHasTrailingDelimiter(): void
	{
		$this->assertTrue(PathKit::hasTrailingDelimiter('foo/bar/'));
		$this->assertTrue(PathKit::hasTrailingDelimiter('foo/bar/   '));
		$this->assertFalse(PathKit::hasTrailingDelimiter('/foo/bar'));
		$this->assertFalse(PathKit::hasTrailingDelimiter('foo\bar\\'));
	}

	/**
	 * @covers PathKit::adjustLeadingDelimiter
	 */
	public function testAdjustLeadingDelimiter(): void
	{
		$this->assertSame('/foo/bar', PathKit::adjustLeadingDelimiter('foo/bar'));
		$this->assertSame('/foo/bar', PathKit::adjustLeadingDelimiter('   foo/bar'));
		$this->assertSame('/foo/bar', PathKit::adjustLeadingDelimiter(' /foo/bar'));
	}

	/**
	 * @covers PathKit::adjustTrailingDelimiter
	 */
	public function testAdjustTrailingDelimiter(): void
	{
		$this->assertSame('foo/bar/', PathKit::adjustTrailingDelimiter('foo/bar'));
		$this->assertSame('foo/bar/', PathKit::adjustTrailingDelimiter('foo/bar   '));
		$this->assertSame('foo/bar/', PathKit::adjustTrailingDelimiter('foo/bar/ '));
	}

	/**
	 * @covers PathKit::removeLeadingDelimiter
	 */
	public function testRemoveLeadingDelimiter(): void
	{
		$this->assertSame('foo/bar', PathKit::removeLeadingDelimiter('/foo/bar'));
		$this->assertSame('foo/bar', PathKit::removeLeadingDelimiter('   /  foo/bar'));
		$this->assertSame('foo/bar', PathKit::removeLeadingDelimiter('foo/bar'));
		$this->assertSame('\foo\bar', PathKit::removeLeadingDelimiter('\foo\bar'));
	}

	/**
	 * @covers PathKit::removeTrailingDelimiter
	 */
	public function testRemoveTrailingDelimiter(): void
	{
		$this->assertSame('foo/bar', PathKit::removeTrailingDelimiter('foo/bar/'));
		$this->assertSame('foo/bar', PathKit::removeTrailingDelimiter('foo/bar  /   '));
		$this->assertSame('/foo/bar', PathKit::removeTrailingDelimiter('/foo/bar'));
		$this->assertSame('foo\bar\\', PathKit::removeTrailingDelimiter('foo\bar\\'));
	}

	public function testExtend(): void
	{
		$this->assertSame('.foo.bar', DotPathKit::normalize(' .. foo... bar   .'));
		$this->assertSame('test.foo/bar', DotPathKit::normalize('foo/bar', 'test'));
		$this->assertSame('test.foo.bar.', DotPathKit::normalize(' foo . bar ', 'test . ', true));
	}
}
