<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/tests/ContainerTest.php
 */

use IrfanTOOR\Container;
use IrfanTOOR\Test;
use IrfanTOOR\Container\ContainerException;

class Service
{
    public $id;

    function __construct($value)
    {
        $this->id = $value;
    }

    function __invoke()
    {
        return $this->id;
    }
}


interface I {}
class C implements I {}

class ContainerTest extends Test
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
		$this->assertImplements( Psr\Container\ContainerInterface::class, $c );
	}

	function testHas()
	{
		$c = $this->getContainer();
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
		$this->assertTrue($c->has('service'));
		$this->assertFalse($c->has(0));
		$this->assertFalse($c->has('undefined'));
		$this->assertFalse($c->has(new Exception));
		$this->assertTrue(isset($c['null']));
		$this->assertTrue(isset($c['hello']));
		$this->assertTrue(isset($c['service']));
		$this->assertFalse(isset($c[0]));
		$this->assertFalse(isset($c['undefined']));
		$this->assertFalse(isset($c[new Exception]));
	}

	/**
	 * throws: ArgumentCountError::class
	 */
	function testHasException()
	{
		$c = $this->getContainer();
		$this->assertException($c->has());
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

	function badEntries()
	{
		return [
			null,
			[],
			new stdClass(),
			['hello' => 'world'],
			0,
			false,
			true,
			2.1,
		];
	}

	/**
	 * throws: Exception::class
	 * message: id must be a string
	 * k: $this->badEntries()
	 */
	function testGetException($k)
	{
		$c = $this->getContainer();
		$c->get($k);
	}

	function unknownEntries()
	{
		return [
			'spider-man',
			'bat-man',
			'unknown',
			'known'
		];
	}

	/**
	 * throws: Exception::class
	 * message: No entry was found for **{$k}**
	 * k: $this->unknownEntries()
	 */
	function testGetUnknownException($k)
	{
		$c = $this->getContainer();
		$c->get($k);
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
	}

	/**
	 * throws: Exception::class
	 * message: id must be a string
	 * k: $this->badEntries()
	 */
	function testSetException($k)
	{
		$c = $this->getContainer();
		$c->set($k, 'something');
	}

	function testSetMultiple()
	{
		$c = $this->getContainer();
		$c->setMultiple(
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

	/**
	 * throws: Exception::class
	 * message: A factory exists with id: factory
	 */
	function testFactoryException()
	{
		$c = $this->getContainer();

		$c->factory('factory', function(){
			return new Service('factory');
		});

		$c->set('factory', 'hello');
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
