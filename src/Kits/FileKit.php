<?php

namespace Diz\Toolkit\Kits;

use Diz\Toolkit\Exceptions\FileSystemException;

class FileKit
{
	/**
	 * Check if the directory at the specified path is empty
	 * @param string $path The path to the directory
	 */
	public static function isDirEmpty(string $path): bool
	{
		return !(new \FilesystemIterator($path))->valid();
	}

	/**
	 * Move or rename the source filename to the destination filename
	 * Returns true if the file has been moved or renamed, false otherwise, throws an exception on error
	 * @throws FileSystemException
	 */
	public static function move(string $source, string $destination): bool
	{
		$source = trim($source);
		$destination = trim($destination);
		if ($source == '' || $destination == '' || $source == $destination) {
			return false;
		}
		$source = realpath($source);
		if ($source == false || realpath($destination) == $source) {
			return false;
		}
		if (strpos('/\\', $destination[-1]) !== false) {
			$destination .= basename($source);
		}
		static::makeDir(dirname($destination));
		if (is_dir($source) && is_dir($destination) && static::isDirEmpty($destination) == false) {
			foreach ((new \FilesystemIterator($source,
				\FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS
			)) as $name => $path) {
				if (static::move($path, $destination . '/' . $name) == false) {
					return false;
				}
			}
			static::delete($source);
			return true;
		}
		if (rename($source, $destination) == false) {
			static::throwLastError('Can\'t move file: ' . $source . ' -> ' . $destination);
		}
		return true;
	}

	/**
	 * Delete the specified file or empty directory
	 * Purge a non-empty directory before if the force flag is true
	 * @throws FileSystemException
	 */
	public static function delete(string $target, bool $force = false): void
	{
		// @ - suppress the errors to avoid race condition
		if (is_dir($target) && is_link($target) == false) {
			if ($force) {
				static::purge($target);
			}
			$result = @rmdir($target);
		} else {
			$result = @unlink($target);
		}
		if ($result == false && file_exists($target)) {
			static::throwLastError('Can\'t delete target: ' . $target);
		}
	}

	/**
	 * Delete all content in the specified path
	 * @throws FileSystemException
	 */
	public static function purge(string $path): void
	{
		foreach ((new \FilesystemIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS)) as $path) {
			static::delete($path, true);
		}
	}

	/**
	 * Try to recursively create a directory with the specified path and mask
	 * @throws FileSystemException
	 */
	public static function makeDir(string $path, int $mask = 0777): void
	{
		// @ - suppress the errors to avoid race condition
		$old_umask = umask(0);
		$result = is_dir($path) || @mkdir($path, $mask, true);
		umask($old_umask);
		if ($result == false && is_dir($path) == false) {
			static::throwLastError('Can\'t create directory: ' . $path);
		}
	}

	/**
	 * Try to link the source with the destination via symlink
	 * Returns true on success, false otherwise
	 * @throws FileSystemException
	 */
	public static function link(string $source, string $destination): bool
	{
		if (function_exists('symlink') == false) {
			return false;
		}
		$destination = rtrim(trim($destination), '/\\');
		$source = realpath($source);
		if (realpath($destination) == $source) {
			return true;
		}
		static::delete($destination);
		static::makeDir(dirname($destination));
		if (symlink($source, $destination) == false) {
			static::throwLastError('Can\'t create link: ' . $source . ' -> ' . $destination);
		}
		return true;
	}

	/**
	 * Read data from the specified file
	 * Throws an exception on error
	 * @param string $filename The name of the target file
	 * @throws FileSystemException
	 */
	public static function read(string $filename): string
	{
		$contents = file_get_contents($filename);
		if ($contents === false) {
			static::throwLastError('Can\'t read from file: ' . $filename);
		}
		return $contents;
	}

	/**
	 * Write data to the specified file
	 * Throws an exception on error
	 * @param string $filename The name of the target file
	 * @param mixed $data Data for writing
	 * @return int The number of bytes that were written to the file
	 * @throws FileSystemException
	 */
	public static function write(string $filename, $data): int
	{
		static::makeDir(dirname($filename));
		$result = file_put_contents($filename, $data);
		if ($result === false) {
			static::throwLastError('Can\'t write to file: ' . $filename);
		}
		return $result;
	}

	/**
	 * @internal
	 * @throws FileSystemException
	 */
	protected static function throwLastError(?string $message = null): void
	{
		throw new FileSystemException(($message ? $message . "\n" : '') . error_get_last()['message']);
	}
}