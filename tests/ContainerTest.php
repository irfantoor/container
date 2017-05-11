<?php
 
use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\Simple;

require_once "TestClass.php";

class SingleTest extends PHPUnit_Framework_TestCase 
{
	function adProvider() {
		$a = [
			"ArrayAdapter",
			"FileAdapter",
			# "FSAdapter"
		];
		
		$d = [
			null,
			"NoCaseDecorator",
			"ReadOnlyDecorator",
			# "Md5Decorator",
		];
		
		$c = [];
		foreach ($a as $aa) {
			foreach ($d as $dd) {
				$c[] = [$aa, $dd];
			}
		}
		
		return $c;
	}

	public function processArray($data) {
		$a = [];
		foreach ($data as $k=>$v) {
			$a[strtolower($k)] = [$k, $v];
		}
		
		return $a;
	}
		
	function getAdapter($adapter, $decorator) {
		$data = [
			'defined' => 'defined',
			'null' 	  => null,
			'array'   => ['k1' => 'v1'],
		];
		
		if ($decorator)
			$data = $this->processArray($data);
		
		switch($adapter) {
			case "FileAdapter":
				$file = __DIR__ . "/" . "file.txt";
				file_put_contents($file, json_encode($data));
				$data = $file;
				break;
				
			case "FSAdapter":
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
		$class = "IrfanTOOR\\Container\\Adapter\\" . $adapter;
		$adapter = new $class($data);
		if ($decorator) {
			$decorator = "IrfanTOOR\\Container\\AdapterDecorator\\" . $decorator;
			return new $decorator($adapter);
		}
		return $adapter;
	}
	
	function getContainer($a, $d) {
		$adapter = $this->getAdapter($a, $d);
		return new Container($adapter);
	}

    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */	
	function testContainerClassExists($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
	    $this->assertInstanceOf('Psr\Container\ContainerInterface', $container);
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
	function testContainerHasAnEntry($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
	
		$this->assertTrue($container->has('defined'));
		$this->assertTrue($container->has('null'));
		$this->assertTrue($container->has('array'));
		$this->assertFalse($container->has('not-defined'));

		$this->assertFalse($container->has(null));
		$this->assertFalse($container->has($this));
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */	
	function testContainerGetEntry($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
			
		$this->assertEquals('defined', $container->get('defined'));
		$this->assertNull($container->get('null'));
		$this->assertArrayHasKey('k1', $container->get('array'));
		$this->assertEquals('v1', $container->get('array')['k1']);
		$this->assertEquals('hello', $container->get("undefind", "hello"));
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */	
    function testContainerGetExceptions($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
		
		# NotFoundException
		$this->expectException(IrfanTOOR\Container\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for **not-defined** identifier');
		$exception = $container->get("not-defined");
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
	function testContainerSetAnEntry($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
	
		if ($decorator !== "ReadOnlyDecorator") {
			# define an entry not previously defined
			$this->assertFalse($container->has('not-defined'));
			$container->set('not-defined', null);
			$this->assertTrue($container->has('not-defined'));
			$this->assertNull($container->get('not-defined'));

			# define an entry previously defined
			$container->set('not-defined', 'now-defined');
			$this->assertTrue($container->has('not-defined'));
			$this->assertEquals('now-defined', $container->get('not-defined'));

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
			$this->expectExceptionMessage('ReadOnly Decorator');
			$exception = $container->set('not-defined', null);			
		}
	}

    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
	function testContainerNotFoundException($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
		
		# NotFoundException
		$this->expectException(IrfanTOOR\Container\NotFoundException::class);
		$this->expectExceptionMessage('No entry was found for **closure** identifier');
		$exception = $container->get('closure');
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
    function testContainerRemoveAnEntry($adpter, $decorator)
	{
		$container = $this->getContainer($adpter, $decorator);
		
		if ($decorator !== "ReadOnlyDecorator") {
			$this->assertEquals("defined", $container->get("defined"));
			$container->remove("defined");
			$this->assertFalse($container->has("defined"));
		}
		else {		
			$this->expectException(IrfanTOOR\Container\Exception::class);
			$this->expectExceptionMessage('ReadOnly Decorator');
			$exception = $container->remove('defined');
		}
	}

    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
	function testRemoveFile($adapter, $decorator) 
	{
		$container = $this->getContainer($adapter, $decorator);
		switch($adapter) {
			case 'FileAdapter':
				$file = __DIR__ . "/" . "file.txt";
				if (file_exists($file))
					unlink($file);
			
				$class = "IrfanTOOR\\Container\\Adapter\\" . $adapter;
									
				$this->expectException(IrfanTOOR\Container\Exception::class);
				$this->expectExceptionMessage("file ***{$file}*** not found");
				$adapter = new $class($file);
				break;
		}
	}
}
