<?php

use PHPUnit\Framework\TestCase;

use IrfanTOOR\Container;

class ContainerTest extends TestCase
{
	function storageNameProvider() {
		return [
			["MemoryStorage"],
			["SqliteStorage"],
			["DirectoryStorage"],
			["HybridStorage"],
		];
	}

	function getStorage($storage_name) {
	
		$ns_storage_name = 'IrfanTOOR\\Storage\\' . $storage_name;
		
        switch ($storage_name) {
        	case 'SqliteStorage':
        		$storage = new $ns_storage_name([
        			'file' => __DIR__ . '/hello.sqlite'
        		]);
        		
        		$storage->clear();
        		break;
			
			case 'DirectoryStorage':
				$path = __DIR__ . '/tmp';
				if (!is_dir($path))
					mkdir($path);
				
        		$storage = new $ns_storage_name('path=' . $path);
        		$storage->clear();
        		break;
        		
        	case 'HybridStorage':
        		$storage = new $ns_storage_name([
        			'path' => __DIR__ . '/tmp'
        		]);
        		
        		$storage->clear();
        		break;        		
        	
        	case 'MemoryStorage':
        	default:
        		$storage = new $ns_storage_name();
        		break;
        }
		
		return $storage;
	}
	
	function getContainer($storage_name) {
		$storage = $this->getStorage($storage_name);

		$c = new Container([
			0       => 'Nothing',
			'null'  => null,
			'hello' => 'world!',
        ], $storage);
        
        return $c;
	}

	function testInstanceOfConnectionString(): void
	{
		$c_string = 'path=/tmp/path;type=test';
		$c_array  = [
			'path' => '/tmp/path',
			'type' => 'test',
		];
		
		$a = new IrfanTOOR\Storage\AbstractStorage($c_string);
		$b = new IrfanTOOR\Storage\AbstractStorage($c_array);
		
		$this->assertEquals($a, $b);
	}
	
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testInstanceOfContainer($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertInstanceOf( IrfanTOOR\Container::class, $c );
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testIdIsAStringWhileGetting($storage_name): void 
	{
		$c = $this->getContainer($storage_name);
			
		$this->assertNull($c->get(0));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testFindEntryById($storage_name): void 
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertNull($c->get('null'));
		$this->assertEquals('world!', $c->get('hello'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testIdNotFoundDefaultFromEmptyContainer($storage_name): void 
	{
		$storage = $this->getStorage($storage_name);
		$c = new Container([], $storage);
		
		$this->assertNull($c->get('something'));
		$this->assertEquals('Hello', $c->get('something', 'Hello'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testIdNotFoundExceptionFromContainerWithValues($storage_name): void 
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertNull($c->get('something'));
		$this->assertEquals('Hello', $c->get('something', 'Hello'));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testIdIsAStringWhileUsingHas($storage_name): void {
		$c = $this->getContainer($storage_name);
		
        $this->assertFalse($c->has(0));
        $this->assertFalse($c->has(null));
        $this->assertTrue($c->has('null'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */			
	function testHasAPredefinedValue($storage_name): void 
	{
		$c = $this->getContainer($storage_name);
		
        $this->assertTrue($c->has('hello'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testDoesNotHaveTheUndefinedValue($storage_name): void 
	{
		$c = $this->getContainer($storage_name);
		
        $this->assertFalse($c->has('something'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testSetUndefinedValue($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertFalse($c->has('something'));
		$c->set('something', 'somevalue');
		$this->assertTrue($c->has('something'));
		$this->assertEquals('somevalue', $c->get('something'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testSetDefinedValue($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertTrue($c->has('null'));
		$this->assertNull($c->get('null'));
		$c->set('null', 0);
		$this->assertTrue($c->has('null'));
		$this->assertNotNull($c->get('null'));
		$this->assertEquals(0, $c->get('null'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */		
	function testSetAnArray($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertFalse($c->has('array'));
		$this->assertFalse($c->has('sky'));
		
		$c->set([
			'array' => ['an', 'array'],
			'sky' => 'blue',
		]);
		
		$this->assertTrue($c->has('array'));
		$this->assertEquals(['an', 'array'], $c->get('array'));
		
		$this->assertTrue($c->has('sky'));
		$this->assertEquals('blue', $c->get('sky'));
	}
	
	/**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testRemoveUndefined($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertFalse($c->has('something'));
		$c->remove('something');
		$this->assertFalse($c->has('something'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testRemoveDefined($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertTrue($c->has('hello'));
		$c->remove('hello');
		$this->assertFalse($c->has('hello'));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testClearContainer($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
		$c->clear();
		$this->assertFalse($c->has('null'));
		$this->assertFalse($c->has('hello'));
	}	

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testToArray($storage_name): void
	{
		$c = $this->getContainer($storage_name);
		
		$this->assertTrue(is_array($c->toArray()));
		$this->assertEquals([
			'null'  => null,
			'hello' => 'world!',
        ], $c->toArray());
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testSetReadOnly($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'null'  => null,
			'hello' => 'world!',
        ], $storage, Container::LOCKED));
		
		$this->assertTrue($c->has('hello'));
		$this->assertEquals('world!', $c->get('hello'));
		$c->set('hello', 'something else');
		$this->assertEquals('world!', $c->get('hello'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testRemoveReadOnly($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'null'  => null,
			'hello' => 'world!',
        ], $storage, Container::LOCKED));
		
		$this->assertTrue($c->has('hello'));
		$c->remove('hello');
		$this->assertTrue($c->has('hello'));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testClearReadOnly($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'null'  => null,
			'hello' => 'world!',
        ], $storage, Container::LOCKED));
		
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
		$c->clear();
		$this->assertTrue($c->has('null'));
		$this->assertTrue($c->has('hello'));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testHasNoCase($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'null'  => null,
			'hello' => 'world!',
        ], $storage, Container::NOCASE));
        
		$this->assertTrue($c->has('hello'));
		$this->assertTrue($c->has('NULL'));
		$this->assertTrue($c->has('HELLO'));
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testGetNoCase($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'Null'  => null,
			'Hello' => 'world!',
        ], $storage, Container::NOCASE));
        
		$this->assertNull($c->get('NULL'));
		$this->assertEquals('world!', $c->get('Hello'));
	}
	
    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */	
	function testSetNoCase($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'Null'  => null,
			'Hello' => 'world!',
        ], $storage, Container::NOCASE));
		
		$this->assertFalse($c->has('something'));
		$this->assertFalse($c->has('SomeThing'));
		$c->set('Something', 'some value');
		$c->set('HELLO', 'WORLD!');
		$this->assertTrue($c->has('something'));
		$this->assertTrue($c->has('SomeThing'));
		
		$this->assertEquals([
			'Null'  => null,
			'Something' => 'some value',
			'HELLO' => 'WORLD!',
        ], $c->toArray());
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */
	function testRemoveNoCase($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'Null'  => null,
			'Hello' => 'world!',
        ], $storage, Container::NOCASE));
        
		$this->assertTrue($c->has('null'));
		$c->remove('Null');
		$this->assertFalse($c->has('null'));
		
		$this->assertEquals([
			'Hello' => 'world!',
		], $c->toArray());
	}

    /**
     * @dataProvider storageNameProvider
     *
     * @param $storage_name
     */
	function testArrayNoCase($storage_name): void
	{
		$storage = $this->getStorage($storage_name);
		
		$c = (new Container([
			'Null'  => null,
			'Hello' => 'world!',
        ], $storage, Container::NOCASE));
		
		$c->set('HELLO', 'WORLD!');
		
		$this->assertEquals([
			'Null' => null,
			'HELLO' => 'WORLD!',
		], $c->toArray());
	}
}
