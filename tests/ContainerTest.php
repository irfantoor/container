<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/tests/ContainerTest.php
 */

require 'Service.php';

use IrfanTOOR\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
	function getContainer($init = null)
	{
		if (!$init)
			$init = [
				'null'    => null,
				'hello'   => 'world!',
				'service' => new Service('value'),
			];

		return new Container($init);
	}

	function testInstanceOfPsrContainer(): void
	{
		$c = $this->getContainer();

		$this->assertInstanceOf( IrfanTOOR\Container::class, $c );
		$this->assertInstanceOf( Psr\Container\ContainerInterface::class, $c );
	}

	function testHas()
	{
		$c = $this->getContainer();
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
		$this->assertTrue($c->has('service'));
		$this->assertFalse($c->has(0));
		$this->assertFalse($c->has('undefined'));

		$this->assertTrue(isset($c['null']));
		$this->assertTrue(isset($c['hello']));
		$this->assertTrue(isset($c['service']));
		$this->assertFalse(isset($c[0]));
		$this->assertFalse(isset($c['undefined']));
	}

	function testGet()
	{
		$c = $this->getContainer();

		$this->assertEquals(null,     $c->get('null'));
		$this->assertEquals('world!', $c->get('hello'));
		$this->assertEquals('value',  $c->get('service'));

		$this->assertEquals(null,     $c['null']);
		$this->assertEquals('world!', $c['hello']);
		$this->assertEquals('value',  $c['service']);
	}

	function testSet()
	{
		$c = $this->getContainer();
		$this->assertFalse($c->has('undefined'));

		$c->set('undefined', new Service('defined!'));

		$this->assertTrue($c->has('undefined'));
		$this->assertEquals('defined!', $c->get('undefined'));

		$this->assertFalse($c->has('service_one'));
		$c->set('service_one', function(){
			return new Service('service_once');
		});
		$v1 = $c->get('service_one');
		$v2 = $c->get('service_one');
		$this->assertInstanceOf(Service::class, $v1);
		$this->assertInstanceOf(Service::class, $v2);
		$this->assertSame($v1, $v2);

		$c['service_two'] = function() {
			return new Service('factory');
		};

		$v1 = $c->get('service_two');
		$v2 = $c->get('service_two');
		$this->assertInstanceOf(Service::class, $v1);
		$this->assertInstanceOf(Service::class, $v2);
		$this->assertSame($v1, $v2);
	}

	function testSetMultiple()
	{
		$c = $this->getContainer();
		$c->set(
			[
				'hello'     => 'another world!',
				'null'      => 'not null',
				'undefined' => 'is defined!',
			]
		);

		$this->assertEquals('another world!', $c['hello']);
		$this->assertEquals('not null',       $c->get('null'));
		$this->assertEquals('is defined!',    $c['undefined']);
	}

	function testFactory()
	{
		$c = $this->getContainer();
		$this->assertFalse($c->has('factory'));
		$c->factory('factory', function(){
			return new Service('factory');
		});
		$v1 = $c->get('factory');
		$v2 = $c->get('factory');
		$this->assertInstanceOf(Service::class, $v1);
		$this->assertInstanceOf(Service::class, $v2);
		$this->assertNotSame($v1, $v2);
	}

	function testRemove()
	{
		$c = $this->getContainer();
		$this->assertFalse($c->has('factory'));
		$c->factory('factory', function(){
			return new Service('factory');
		});
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
		$this->assertTrue($c->has('service'));
		$this->assertTrue($c->has('factory'));

		$c->remove('null');
		$c->remove('factory');
		$this->assertFalse($c->has('null'));
		$this->assertFalse($c->has('factory'));
	}

	function testToArray()
	{
		$init = [
			'null'    => null,
			'hello'   => 'world!',
			'service' => new Service('value'),
		];

		$c = $this->getContainer($init);

		$this->assertEquals($init, $c->toArray());
	}

	function testKeys()
	{
		$init = [
			'null'    => null,
			'hello'   => 'world!',
			'service' => new Service('value'),
		];

		$c = $this->getContainer($init);
		$this->assertEquals(array_keys($init), $c->keys());
	}

	function testCount()
	{
		$c = $this->getContainer();
		$this->assertEquals(count($c->toArray()), $c->count());
	}

	function testIterator()
	{
		$c = $this->getContainer();
		$keys = [];
		$array = [];
		foreach($c as $k=>$v) {
			$keys[] = $k;
			$array[$k] = $v;
		}

		$this->assertEquals($keys, $c->keys());
		$this->assertEquals($array, $c->toArray());
	}
}
