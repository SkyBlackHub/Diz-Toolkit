<?php

namespace Diz\Toolkit\Tests\Tools;

use PHPUnit\Framework\TestCase;

use Diz\Toolkit\Tools\Options;

final class OptionsTest extends TestCase
{
	public function testOptions(): void
	{
		$data = [
			'foo' => ' BAR ',
			'int' => 555,
			'sup' => [
				'test' => 123
			],
			'true' => true,
			'false' => false
		];

		$options = new Options($data);

		$this->assertSame(' BAR ', $options->get('foo'));
		$this->assertSame('BAR', $options->getUsefulString('foo'));
		$this->assertSame('BAR', $options->get('not_foo', 'BAR'));
		$this->assertSame(null, $options->get('not_foo'));

		$this->assertFalse($options->has('nope'));
		$this->assertFalse($options->is('nope'));
		$this->assertFalse($options->is('false'));
		$this->assertTrue($options->is('true'));

		$this->assertTrue($options->not('false'));
		$this->assertFalse($options->not('true'));

		$this->assertSame(null, $options->getString('sup'));

		$branch = $options->branch('sup');
		$this->assertNotNull($branch);
		$this->assertSame(123, $branch->get('test'));

		$branch = $options->branch('zilch');
		$this->assertNull($branch);

		$options->set('test', 77.8);
		$this->assertSame(77, $options->getInteger('test'));
		$this->assertSame(77.8, $options->getFloat('test'));

		$this->assertSame(true, $options->getBoolean('true'));
		$this->assertSame(false, $options->getBoolean('false'));
		$this->assertSame(false, $options->getBoolean('zilch', false));
	}
}