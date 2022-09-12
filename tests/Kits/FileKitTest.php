<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\FileKit;

use Diz\Toolkit\Exceptions\FileSystemException;

final class FileKitTest extends TestCase
{
	private static function rootDir(): string
	{
		return __DIR__ . '/..';
	}

	public static function setUpBeforeClass(): void
	{
		chdir(self::rootDir());
		FileKit::delete('sandbox', true);
		FileKit::makeDir('sandbox');
		chdir('sandbox');
	}

	public static function tearDownAfterClass(): void
	{
		chdir(self::rootDir());
		FileKit::delete('sandbox', true);
	}

	/**
	 * @covers FileKit::makeDir
	 */
	public function testMakeDir(): void
	{
		$test = 'dir1';
		FileKit::makeDir($test);
		FileKit::makeDir($test);
		$this->assertTrue(is_dir($test));
		$this->assertTrue(FileKit::isDirEmpty($test));
		$test = 'dir1/dir2/dir3';
		FileKit::makeDir($test);
		$this->assertTrue(is_dir($test));
		$this->assertTrue(FileKit::isDirEmpty($test));
	}

	/**
	 * @covers FileKit::write
	 * @covers FileKit::read
	 */
	public function testWriteRead(): void
	{
		FileKit::write('1', 'foo');
		FileKit::write('2', 'bar');
		$this->assertTrue(is_file('1'));
		$this->assertTrue(is_file('2'));
		$this->assertSame('foo', FileKit::read('1'));
	}

	/**
	 * @covers FileKit::link
	 */
	public function testLink(): void
	{
		FileKit::write('t', 'foo');
		$this->assertTrue(is_file('t'));
		FileKit::link('t', 'l');
		$this->assertTrue(is_link('l'));
		FileKit::write('l', 'bar');
		$this->assertSame('bar', FileKit::read('l'));
	}

	/**
	 * @covers FileKit::move
	 */
	public function testMove(): void
	{
		FileKit::makeDir('move1');
		$this->assertTrue(is_dir('move1'));
		chdir('move1');

		FileKit::write('m1', 'foo');
		$this->assertTrue(is_file('m1'));

		FileKit::write('m2', 'bar');
		$this->assertTrue(is_file('m2'));

		chdir('..');

		FileKit::makeDir('move2');
		$this->assertTrue(is_dir('move2'));

		FileKit::move('move1/m1', 'move2/m1');
		$this->assertTrue(is_file('move2/m1'));
		$this->assertFalse(is_file('move1/m1'));

		FileKit::move('move1/m2', 'move2/');
		$this->assertTrue(is_file('move2/m2'));
		$this->assertFalse(is_file('move1/m2'));

		$this->assertTrue(FileKit::isDirEmpty('move1'));

		FileKit::move('move2', 'move3');
		$this->assertTrue(is_file('move3/m1'));
		$this->assertTrue(is_file('move3/m2'));
		$this->assertFalse(is_dir('move2'));

		FileKit::makeDir('move4');
		FileKit::write('move4/m3', '123');

		FileKit::move('move3', 'move4');
		$this->assertTrue(is_file('move4/m1'));
		$this->assertTrue(is_file('move4/m2'));
		$this->assertTrue(is_file('move4/m3'));
		$this->assertFalse(is_dir('move3'));
	}

	/**
	 * @covers FileKit::delete
	 */
	public function testDelete(): void
	{
		FileKit::makeDir('delete');
		$test = 'delete/file';
		FileKit::write($test, 'foo');
		$this->assertTrue(is_file($test));
		FileKit::delete($test);
		$this->assertFalse(is_file($test));
		FileKit::write($test, 'bar');
		// trying to delete a non-empty directory without the force flag enabled
		$this->expectException(FileSystemException::class);
		FileKit::delete('delete');
		$this->assertTrue(is_dir('delete'));

		$this->expectException(null);
		FileKit::delete('delete', true);
		$this->assertFalse(is_dir('delete'));
	}
}