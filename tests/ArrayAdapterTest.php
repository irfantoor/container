<?php

require_once "Provider.php";

use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\{AdapterInterface, AbstractAdapter, ArrayAdapter};
use IrfanTOOR\Test;

class ArrayAdapterTest extends Test
{
    function testInstance()
    {
        $s = new ArrayAdapter();

        $this->assertInstanceOf(ArrayAdapter::class, $s);
        $this->assertInstanceOf(AbstractAdapter::class, $s);
        $this->assertImplements(AdapterInterface::class, $s);
    }

    function testArrayOperations()
    {
        $s = new ArrayAdapter();

        $init = Provider::goodInitArgs();

        foreach ($init as $k => $v) {
            $this->assertFalse($s->has($k));
            $this->assertFalse($s->remove($k));
            $this->assertTrue($s->set($k, $v));
            $this->assertTrue($s->has($k));
            $this->assertEquals($v, $s->get($k));
            $this->assertTrue($s->remove($k));
            $this->assertFalse($s->has($k));
        }
    }

    function testContainerOperations()
    {
        $c = new Container();

        $init = Provider::goodInitArgs();

        foreach ($init as $k => $v) {
            $this->assertFalse($c->has($k));
            # throws an exception rather
            // $this->assertFalse($c->remove($k));
            $this->assertTrue($c->set($k, $v));
            $this->assertTrue($c->has($k));
            $this->assertEquals($v, $c->get($k));
            $this->assertTrue($c->remove($k));
            $this->assertFalse($c->has($k));
        }
    }
}
