<?php
 
use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\Simple;
use IrfanTOOR\Container\Adapter\DecoratorNoCase;
 
class ContainerDecoratorTest extends PHPUnit_Framework_TestCase 
{
	public function adapterProvider() {
		return [
			["Simple",    "readwrite"],
			["File",      "readwrite"],
			["Config",    "readonly" ],
		];
	}
	
	public function containerProvider() {
		return [
			["Container"],
		];
	}
	
	public function processArray($data) {
		$a = [];
		foreach ($data as $k=>$v) {
			$a[strtolower($k)] = ["id"=>$k, "value"=>$v];
		}
		
		return $a;
	}
	
	public function getAdapter($type) {
		$data = $this->processArray([
			'defined' => 'defined',
			'null' 	  => null,
			'array'   => ['k1' => 'v1'],
		]);
		
		
		switch($type) {
			case "File":
			case "Config":
				$file = __DIR__ . "/" . "file.txt";
				file_put_contents($file, json_encode($data));
				$data = $file;
				break;
				
			case "Directory":
				$dir = __DIR__ . "/dir";
				if (!file_exists($dir))
					mkdir ($dir);
				
				foreach($data as $k=>$v) {
					file_put_contents($dir . "/" . $k, json_encode($v));
				}
				$data = $dir;
				break;
				
			default:
		}
		
		# $adapter = $prophecy->reveal($data);
		$class = "IrfanTOOR\\Container\\Adapter\\" . $type;
		$adapter = new $class($data);
		$decorator = new DecoratorNoCase($adapter);
		
		return $decorator;
	}
	
	function getContainer($type, $atype) {
		$adapter = $this->getAdapter($atype);
		$class = "IrfanTOOR\\" . $type;
		$container = new $class($adapter);
		return $container;
	}
	
    /**
     * @dataProvider containerProvider
     *
     * @param $type
     */
	public function testContainerClassExists($type)
	{
		$container = $this->getContainer($type, "Simple");
	    $this->assertInstanceOf('Psr\Container\ContainerInterface', $container);
	}
	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     */
	public function testContainerHasAnEntry($type)
	{
		foreach($this->containerProvider() as $v) {
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);
		
			$this->assertTrue($container->has('Defined'));
			$this->assertTrue($container->has('NULL'));
			$this->assertTrue($container->has('ARray'));
			$this->assertFalse($container->has('not-DEFINED'));

			$this->assertFalse($container->has(null));
			$this->assertFalse($container->has($this));
		}
	}
	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     */	
	public function testContainerGetEntry($type)
	{
		foreach($this->containerProvider() as $v) {
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);
				
			$this->assertEquals('defined', $container->get('Defined'));
			$this->assertNull($container->get('NULL'));
			$this->assertArrayHasKey('k1', $container->get('ARray'));
			$this->assertEquals('v1', $container->get('ARray')['k1']);
			$this->assertEquals('hello', $container->get("undefind", "hello"));
		}
	}
	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     */		
	public function testContainerGetExceptions($type)
	{
		foreach($this->containerProvider() as $v) {
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);
			
			# NotFoundException
			$this->expectException(IrfanTOOR\Container\NotFoundException::class);
			$this->expectExceptionMessage('No entry was found for **not-defined** identifier');
			$exception = $container->get("not-defined");
		}
	}
	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     * @param $readwrite
     */	
	public function testContainerSetAnEntry($type, $readwrite)
	{
		foreach($this->containerProvider() as $v) 
		{
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);
		
			if ($readwrite == "readwrite") {				
				# define an entry not previously defined
				$this->assertFalse($container->has('not-defined'));
				$container->set('not-defined', null);
				$this->assertTrue($container->has('not-Defined'));
				$this->assertNull($container->get('not-DEFINED'));

				# define an entry previously defined
				$container->set('not-defined', 'now-defined');
				$this->assertTrue($container->has('not-Defined'));
				$this->assertEquals('now-defined', $container->get('not-DEFINED'));

				# Set a class
				$class = new TestClass('hello');
				$container->set('class', $class);

				$c1 = $container->get('class');
				$c2 = $container->get('class');

				$this->assertInstanceOf('TestClass', $c1);
				$this->assertEquals($c1, $c2);
				$this->assertSame($c1, $c2);
				$this->assertEquals("hello", $c1->value());
				$this->assertSame($c1->value, $c2->value);

				# Set a closure
				$container->set('closure', function($arg=null){
					 return new TestClass($arg);
				});
		
		
				$c = $container->get('closure');
				$c1 = $c("hello");
				$c2 = $c("hello");
				$c3 = $c();
		
				$this->assertInstanceOf('TestClass', $c1);
				$this->assertEquals($c1, $c2);
				$this->assertNotSame($c1, $c2);
				$this->assertEquals("hello", $c1->value());
				$this->assertNull($c3->value());
			}
			else {
				$this->expectException(IrfanTOOR\Container\Exception::class);
				$this->expectExceptionMessage('Config container is a readonly container');
				$exception = $container->set('not-defined', null);			
			}
		}			
	}

	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     */
	public function testContainerNotFoundException($type)
	{
		foreach($this->containerProvider() as $v) 
		{
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);

			# NotFoundException
			$this->expectException(IrfanTOOR\Container\NotFoundException::class);
			$this->expectExceptionMessage('No entry was found for **closure** identifier');
			$exception = $container->get('closure');
		}
	}
	
    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     * @param $readwrite
     */	
	public function testContainerRemoveAnEntry($type, $readwrite)
	{
		foreach($this->containerProvider() as $v) 
		{
			$cname = $v[0];
			$container = $this->getContainer($cname, $type);	
	
			if ($readwrite == "readwrite") {
				$this->assertEquals("defined", $container->get("Defined"));
				$container->remove("DEFINED");
				$this->assertFalse($container->has("Defined"));
			}
			else {		
				$this->expectException(IrfanTOOR\Container\Exception::class);
				$this->expectExceptionMessage('Config container is a readonly container');
				$exception = $container->remove('defined');
			}
		}
	}

    /**
     * @dataProvider adapterProvider
     *
     * @param $type
     * @param $readwrite
     */		
	public function testRemoveFile($type, $readwrite) {
		$file = __DIR__ . "/" . "file.txt";
		if (file_exists($file))
			unlink($file);

		foreach($this->containerProvider() as $v) 
		{
			switch($type) {
				case 'File':
				case 'Config':
					$class = "IrfanTOOR\\Container\\Adapter\\" . $type;
										
					$this->expectException(IrfanTOOR\Container\Exception::class);
					$this->expectExceptionMessage("file ***{$file}*** not found");
					$adapter = new $class($file);
					break;
			}			
		}
	}
}
