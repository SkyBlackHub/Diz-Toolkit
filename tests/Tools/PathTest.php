<?php

namespace Diz\Toolkit\Tests\Tools;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Tools\Path;

final class PathTest extends TestCase
{
	public function testPath(): void
	{
		$this->assertSame([], (new Path(''))->getAll());
		$this->assertSame(['foo', 'bar'], (new Path('foo/bar'))->getAll());
		$this->assertSame(['foo', 'bar'], (new Path('foo.bar', Path::DELIMITER_DOT))->getAll());
		$this->assertSame([' Foo ', ' Bar '], (new Path(' Foo / Bar ', Path::DEFAULT_DELIMITER, Path::OPTION_NONE))->getAll());
		$this->assertSame(['foo', 'bar'], (new Path(' Foo / Bar ', Path::DEFAULT_DELIMITER, Path::OPTIONS_UNIFIED))->getAll());
		$this->assertSame(['foo', '', 'bar'], (new Path(' Foo // Bar ', Path::DEFAULT_DELIMITER, Path::OPTIONS_UNIFIED ^ Path::OPTION_CLEAN))->getAll());

		$path = new Path('first/middle/last');

		$this->assertTrue($path->isExists(1));
		$this->assertFalse($path->isEmpty());

		$this->assertSame(1, $path->getIndexOf('middle'));

		$this->assertSame('first', $path->getFirst());
		$this->assertSame('last', $path->getLast());
		$this->assertSame('middle', $path->getAt(1));
		$this->assertSame(1, $path->getIndexOf('middle'));

		$path->append('new');
		$this->assertSame(['first', 'middle', 'last', 'new'], $path->getAll());

		$path->remove('new');
		$this->assertSame(['first', 'middle', 'last'], $path->getAll());

		$path->prepend('new');
		$this->assertSame(['new', 'first', 'middle', 'last'], $path->getAll());

		$path->removeFirst();
		$this->assertSame(['first', 'middle', 'last'], $path->getAll());

		$path->insertAt(2, 'new');
		$this->assertSame(['first', 'middle', 'new', 'last'], $path->getAll());

		$path->insertAt(4, 'new');
		$this->assertSame(['first', 'middle', 'new', 'last', 'new'], $path->getAll());
		$this->assertSame(5, $path->getCount());

		$path->removeAll('new');
		$this->assertSame(['first', 'middle', 'last'], $path->getAll());
		$this->assertSame('first/middle/last', $path->join());

		$path->removeLast();
		$this->assertSame(['first', 'middle'], $path->getAll());


		$path[0] = 'foo';
		$this->assertSame(['foo', 'middle'], $path->getAll());

		$path[2] = 'bar';
		$this->assertFalse(isset($path[2]));
		$this->assertSame(['foo', 'middle'], $path->getAll());

		$path[] = 'bar';
		$this->assertTrue(isset($path[2]));
		$this->assertSame(['foo', 'middle', 'bar'], $path->getAll());

		unset($path[1]);
		$this->assertSame(['foo', 'bar'], $path->getAll());

		$path->appendPath('Sub.Zero', Path::DELIMITER_DOT, Path::OPTIONS_UNIFIED);
		$this->assertSame(['foo', 'bar', 'sub', 'zero'], $path->getAll());
		$this->assertSame(['bar', 'sub'], $path->sub(1, 2)->getAll());

		$data = [
			'foo' => [
				'bar' => 123,
				'sub' => [
					'zero' => 0
				],
				'bor' => 777,
				'bir' => 111,
				'*' => 'fallback'
			]
		];

		$path->setPath('foo/bar');
		$this->assertSame(123, $path->read($data));

		$path->setPath('foo/bar2');
		$this->assertSame(null, $path->read($data));
		$this->assertSame(123, $path->read($data, 123));
		$this->assertSame('fallback', $path->read($data, null, Path::TRAVERSE_FALLBACK));

		$path->setPath('foo/bo?');
		$this->assertSame(777, $path->read($data, null, Path::TRAVERSE_WILDCARDS));

		$path->setPath('foo/b*');
		$this->assertSame(123, $path->read($data, null, Path::TRAVERSE_WILDCARDS));

		$path->setPath('foo/bar');
		$this->assertSame(1, $path->write($data, 456));
		$this->assertSame(456, $data['foo']['bar']);

		$path->setPath('foo/new');
		$this->assertSame(1, $path->write($data, 123));
		$this->assertSame(123, $data['foo']['new']);

		$path->setPath('foo/new/bad');
		$this->assertSame(0, $path->write($data, 123));

		$path->setPath('foo/b*');
		$this->assertSame(1, $path->write($data, 222, Path::TRAVERSE_WILDCARDS));
		$this->assertSame(222, $data['foo']['bar']);

		$path->setPath('foo/b*');
		$this->assertSame(3, $path->write($data, 333, Path::TRAVERSE_WILDCARDS | Path::TRAVERSE_COVERAGE));
		$this->assertSame(333, $data['foo']['bar']);
		$this->assertSame(333, $data['foo']['bor']);
		$this->assertSame(333, $data['foo']['bir']);

		$data['foo']['sup'] = ['ignore' => 'me'];

		$path->setPath('foo/s*/z*');
		$this->assertSame(1, $path->write($data, 333, Path::TRAVERSE_WILDCARDS | Path::TRAVERSE_COVERAGE));
		$this->assertSame(333, $data['foo']['sub']['zero']);

		$path->setPath('foo/s*/*');
		$this->assertSame(2, $path->write($data, 111, Path::TRAVERSE_WILDCARDS | Path::TRAVERSE_COVERAGE));
		$this->assertSame(111, $data['foo']['sub']['zero']);
		$this->assertSame(111, $data['foo']['sup']['ignore']);
	}
}