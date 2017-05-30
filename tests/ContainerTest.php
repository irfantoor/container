<?php
 
use IrfanTOOR\Container;

require_once "TestClass.php";

class ContainerTest extends PHPUnit_Framework_TestCase 
{
	function adProvider() {
		$a = [
			null,
			"ArrayAdapter",
			"FileAdapter",
			#"FileSystemAdapter"
		];
		
		$d = [
			null,
			"NullDecorator",
			"NoCaseDecorator",
			"ReadOnlyDecorator",
			# "MD5Decorator",
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
		
	function getData($adapter, $decorator) {
	
		$a = $adapter ? "IrfanTOOR\\Container\\Adapter\\" . $adapter :
			"IrfanTOOR\\Container\\Adapter\\ArrayAdapter";
		
		$d = "IrfanTOOR\\Container\\Decorator\\" . $decorator;
		
		if ($decorator == "NoCaseDecorator") {
			$data = [
				'DefineD' => 'defined',
				'NULL' 	  => null,
				'Array'   => ['k1' => 'v1'],
			];
		}
		else {
			$data = [
				'defined' => 'defined',
				'null' 	  => null,
				'array'   => ['k1' => 'v1'],
			];		
		}
		
		switch($adapter) {
			case null:
				if ($decorator)
					$ad = new $d($data);
				else
					$ad = $data;
				break;
			
			case "ArrayAdapter":
				if ($decorator)
					$ad = new $d($data);
				else
					$ad = new $a($data);
				break;
				
			case "FileAdapter":
				$file = __DIR__ . "/" . "file";
				$initialized = false;
				
				if (file_exists($file))
					unlink($file);
								
				if ($decorator) {
					if ($decorator == "ReadOnlyDecorator")	{
						$c = new Container($file);
						$c->set($data);
						$initialized = true;						
					}
					$ad = new $d($file);
				}
				else {
					$ad = new $a($file);
				}
				
				if (!$initialized)
					$ad->set($data);

				break;
				
			case "FileSystemAdapter":
				$path = __DIR__ . "/" . "test";
				# if (is_dir($path)) {
				# 	system('rm -r ' . $path);
					#rmdir($path);
				# }
				# mkdir($path);
				$ad = new $a($path);
				$ad->set($data);
				break;
				
			default:
				
		}

		return $ad;
	}
	
	function getContainer($adapter, $decorator) {
		$d = $this->getData($adapter, $decorator);
		return new Container($d);
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
	   	$this->assertInstanceOf('IrfanTOOR\Container', $container);
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
	function testContainerHasAnEntry($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
	
		$this->assertTrue($container->has('defined'));
		$this->assertTrue($container->has('null'));
		$this->assertTrue($container->has('array'));
		$this->assertFalse($container->has('not-defined'));
	}
	
    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */	
	function testContainerGetEntry($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
			
		$this->assertEquals('defined', $container->get('defined'));
		$this->assertNull($container->get('null'));
		$this->assertEquals("NULL", $container->get('undefined', "NULL"));
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
	function testContainerGetException($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
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
	function testContainerSetAnEntry($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
		
		if ($decorator != "ReadOnlyDecorator") {
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
			# define an entry not previously defined
			$this->assertFalse($container->has('not-defined'));
			$container->set('not-defined', null);
			$this->assertFalse($container->has('not-defined'));
			$this->assertEquals("NULL",$container->get('not-defined', "NULL"));

			# define an entry previously defined
			$container->set('not-defined', 'now-defined');
			$this->assertFalse($container->has('not-defined'));
			$this->assertEquals("NULL",$container->get('not-defined', "NULL"));

			# Set a class
			$class = new TestClass('hello');
			$container->set('class', $class);

			$c1 = $container->get('class',"NULL");
			$c2 = $container->get('class', "NULL");

			$this->assertEquals("NULL", $c1);
			$this->assertEquals($c1, $c2);
			$this->assertSame($c1, $c2);
			# $this->assertEquals("hello", $c1->value());
			# $this->assertSame($c1->value, $c2->value);

			# Set a closure
			$container->set('closure', function($arg=null){
				 return new TestClass($arg);
			});

			$c = $container->get('closure', "NULL");
			$this->assertEquals("NULL", $c);
		}
	}

    /**
     * @dataProvider adProvider
     *
     * @param $adapter
     * @param $decorator
     */
    function testContainerRemoveAnEntry($adapter, $decorator)
	{
		$container = $this->getContainer($adapter, $decorator);
		
		$this->assertEquals("defined", $container->get("defined"));
		$container->remove("defined");
		if ($decorator != "ReadOnlyDecorator") {
			$this->assertFalse($container->has("defined"));
		}
		else {
			$this->assertTrue($container->has("defined"));
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
		 $file = __DIR__ . "/" . "file";
		 if (file_exists($file))
		  	unlink($file);
			
		 # system("rm -r " . __DIR__ . "/test");
	}
}
