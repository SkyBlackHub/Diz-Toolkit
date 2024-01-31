<?php

namespace Diz\Toolkit\Tests\Kits;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Kits\URLKit;

final class URLKitTest extends TestCase
{
	/**
	 * @covers URLKit::compose
	 */
	public function testCompose()
	{
		$this->assertSame('https://google.com/?search=foo', URLKit::compose([
			'scheme' => 'https',
			'host' => 'google.com',
			'query' => 'search=foo'
		]));

		$this->assertSame('http://google.com/#bar', URLKit::compose([
			'scheme' => 'http',
			'host' => 'google.com',
			'fragment' => 'bar'
		]));

		$this->assertSame('https://user:password@google.com/search?q=foo', URLKit::compose([
			'host'  => 'google.com',
			'user'  => 'user',
			'pass'  => 'password',
			'path'  => '/search',
			'query' => 'q=foo'
		]));

		$this->assertSame('https://google.com/search?q[]=foo&q[]=bar', URLKit::compose([
			'host'  => 'google.com',
			'path'  => '/search',
			'query' => ['q' => ['foo', 'bar']]
		]));

		$this->assertSame('https://google.com/search?q[0]=foo&q[1]=bar', URLKit::compose([
			'host'  => 'google.com',
			'path'  => '/search',
			'query' => ['q' => ['foo', 'bar']]
		], ['numeric_indices_mode' => URLKit::QUERY_KEEP_NUMERIC_INDICES]));

		$this->assertSame('https://google.com/search?q=foo&q=bar', URLKit::compose([
			'host'  => 'google.com',
			'path'  => '/search',
			'query' => ['q' => ['foo', 'bar']]
		], ['numeric_indices_mode' => URLKit::QUERY_FLAT_NUMERIC_INDICES]));
	}

	/**
	 * @covers URLKit::extend
	 */
	public function testExtend(): void
	{
		$this->assertSame('https://user:password@google.com/search?q=foo', URLKit::extend('//google.com', '//user:password@test.com', 'search?q=foo'));
	}

	/**
	 * @covers URLKit::encode
	 * @testdox Encode URL
	 */
	public function testEncode(): void
	{
		$this->assertSame('foo[bar]=r%C3%A1pida', URLKit::encode('foo[bar]=rápida'));
		$this->assertSame('100%25/%D1%81%D0%BB%D0%BE%D0%B2%D0%BE', URLKit::encode('100%/слово'));
	}

	/**
	 * @covers URLKit::basepath
	 */
	public function testBasepath(): void
	{
		$this->assertSame('foo/', URLKit::basepath('foo/'));
		$this->assertSame('foo/', URLKit::basepath('foo/bar'));
		$this->assertSame('', URLKit::basepath('foo'));
		$this->assertSame('/', URLKit::basepath('/foo'));
		$this->assertSame('some.com/', URLKit::basepath('some.com/foo?q=123'));
	}

	/**
	 * @covers URLKit::complete
	 */
	public function testComplete(): void
	{
		$this->assertSame('https://test.com/foo', URLKit::complete('foo', '//test.com'));
		$this->assertSame('https://test.com/bar', URLKit::complete('bar', '//test.com/foo'));
		$this->assertSame('https://test.com/foo/bar', URLKit::complete('bar', '//test.com/foo/'));
		$this->assertSame('https://test.com/foo/bar/', URLKit::complete('bar/', '//test.com/foo/'));
		$this->assertSame('http://test.com:8080/foo/bar/other?q=555#here', URLKit::complete('bar/other?q=555#here', 'http://test.com:8080/foo/test?q=123#there'));
		$this->assertSame('http://other.com/bar/', URLKit::complete('//other.com/bar/', 'http://test.com/foo/'));
		$this->assertSame('https://localhost/', URLKit::complete([], []));
	}

	/**
	 * @covers URLKit::buildQuery
	 */
	public function testBuildQuery(): void
	{
		$this->assertSame('foo=bar', URLKit::buildQuery(['foo' => 'bar']));
		$this->assertSame('0=foo&1=bar', URLKit::buildQuery(['foo', 'bar']));
		$this->assertSame('test[]=foo&test[]=bar', URLKit::buildQuery(['foo', 'bar'], 'test'));
		$this->assertSame('test[2]=foo&test[5]=bar', URLKit::buildQuery([2 => 'foo', 5 => 'bar'], 'test', URLKit::QUERY_KEEP_NUMERIC_INDICES));
		$this->assertSame('test=foo&test=bar', URLKit::buildQuery([2 => 'foo', 5 => 'bar'], 'test', URLKit::QUERY_FLAT_NUMERIC_INDICES));
		$this->assertSame('foo=bar&test=123', URLKit::buildQuery(['foo' => 'bar', 'test' => 123]));
		$this->assertSame('foo[bar]=123&foo[test]=555', URLKit::buildQuery(['foo' => ['bar' => 123, 'test' => 555]]));
		$this->assertSame('foo[]=a&foo[]=b&foo[]=c', URLKit::buildQuery(['foo' => ['a', 'b', 'c']]));
		$this->assertSame('foo=b%26w&100%25=r%C3%A1pida', URLKit::buildQuery(['foo' => 'b&w', '100%' => 'rápida']));
	}
}