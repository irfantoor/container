<?php

namespace {
require_once "Provider.php";

use IrfanTOOR\Container;
use IrfanTOOR\Container\Extension\{Factory};
use IrfanTOOR\Test;

use IrfanTOOR\Container\Adapter\AdapterInterface;
use IrfanTOOR\Container\Adapter\ArrayAdapter;

use Test\MockFactory;
use Test\ii;
use Test\cc;

class ExtensionTest extends Test
{
    function testInstance()
    {
        $f = new MockFactory();

        $this->assertInstanceOf(Factory::class, $f);

        $c = new Container([
            'hello' => 'world!'
        ]);

        $c->addExtension('factory', $f);

        # using interface name
        $this->assertNotMethod('factory', $c);
        $this->assertNotException($c->factory(ii::class, cc::class));

        # long name (with namespace)
        $cc = $c->create(ii::class);
        $this->assertInstanceOf(cc::class, $cc);

        # short name (without namespace)
        $cc = $c->create('ii');
        $this->assertInstanceOf(cc::class, $cc);

        # long name (with namespace)
        $cc = $c->create(cc::class);
        $this->assertInstanceOf(cc::class, $cc);
        
        # short name of class
        $cc = $c->create('cc');
        $this->assertInstanceOf(cc::class, $cc);

        # calling a function which exists in container or in any other extension
        $this->assertEquals('world!', $c->get('hello'));
        $this->assertEquals(MockFactory::class . '::get_hello', $c->call('factory', 'get', ['hello']));
    }

    function testHttp()
    {
        $c = new Container();
        $f = new Factory([
            'ArrayAdapter' => ArrayAdapter::class,
        ]);

        $c->addExtension('factory', $f);
        $aa = $c->create(ArrayAdapter::class, ['hello' => 'world']);

        $this->assertInstanceOf(ArrayAdapter::class, $aa);
    }
}
}

namespace Test {
    use IrfanTOOR\Container\Extension\{Factory};

    interface ii {}
    class cc implements ii {}

    class MockFactory extends Factory
    {
        public function get(string $id)
        {
            return __CLASS__ . '::' . __FUNCTION__ . '_' . $id;
        }
    }    
}
