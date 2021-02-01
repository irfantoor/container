<?php

require_once "Provider.php";

use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\ArrayAdapter;
use IrfanTOOR\Container\ContainerException;
use IrfanTOOR\Container\NotFoundException;
use IrfanTOOR\Test;
use Psr\Container\ContainerInterface;

class ContainerTest extends Test
{
    function testInstance()
    {
        $c = new Container();
        $this->assertInstanceOf(Container::class, $c);
        $this->assertImplements(ContainerInterface::class, $c);
        $this->assertImplements(ArrayAccess::class, $c);

        # default adapter is ArrayAdapter
        $c = new MockContainer();
        $this->assertInstanceOf(ArrayAdapter::class, $c->getAdapter());
    }

    /**
     * throws: IrfanTOOR\Container\ContainerException::class
     * message: Container can be initialized with an associative array only
     * b: Provider::badInitArgs()
     */
    function testInvalidConstruct($b)
    {
        $c = new Container($b);
    }

    /**
     * throws: IrfanTOOR\Container\ContainerException::class
     * message: Identifier can only be a non empty string.
     * b: Provider::badKeys()
     */
    function testInvalidKeys($b)
    {
        $c = new Container();
        $c->set($b, 'hello');
    }

    function testValidConstruct()
    {
        $c = new Container();
        $this->assertInstanceOf(Container::class, $c);

        $c = new Container([]);
        $this->assertInstanceOf(Container::class, $c);

        $init = Provider::goodInitArgs();
        $c = new Container($init);
        $this->assertInstanceOf(Container::class, $c);

        foreach ($init as $k => $v) {
            $this->assertEquals($v, $c->get($k));
        }

        $c = new Container();
        foreach ($init as $k => $v) {
            $this->assertTrue($c->set($k, $v));
            $this->assertEquals($v, $c->get($k));
        }
    }

    /**
     * throws: IrfanTOOR\Container\ContainerException::class
     * message: Identifier can only be a non empty string.
     * b: Provider::badKeys()
     */
    function testGetContainerException($b)
    {
        $c = new Container(['hello' => 'world']);
        $c->get($b);
    }

    /**
     * throws: IrfanTOOR\Container\NotFoundException::class
     * message: No entry was found for {$k} identifier.
     * k: Provider::goodKeys()
     */
    function testGetNotFoundException($k)
    {
        $c = new Container(['unknown' => 'not known']);
        $c->get($k);
    }

    function testGet()
    {
        $init = Provider::goodInitArgs();
        $c = new Container($init);
        foreach ($init as $k => $v) {
            $this->assertEquals($v, $c->get($k));
        }
    }

    function testHas()
    {
        $c = new Container();

        foreach (Provider::goodKeys() as $k) {
            $this->assertFalse($c->has($k));
        }

        $c = new Container(Provider::goodInitArgs());

        # throws no exception
        foreach (Provider::badInitArgs() as $k) {
            if (is_string($k))
                continue;
            $this->assertFalse($c->has($k));
        }

        # present keys
        foreach (Provider::goodInitArgs() as $k => $v) {
            $this->assertTrue($c->has($k));
        }

        # throws no exception
        foreach (Provider::badKeys() as $k) {
            $this->assertFalse($c->has($k));
        }
    }

    /**
     * throws: IrfanTOOR\Container\ContainerException::class
     * message: Identifier can only be a non empty string.
     * b: Provider::badKeys()
     */
    function testSetException($b)
    {
        $c = new Container();
        $c->set($b, "something");
    }

    /**
     * k: Provider::goodKeys()
     */
    function testSet($k)
    {
        $c = new Container();

        $this->assertFalse($c->has($k));
        $c->set($k, $k);
        $this->assertEquals($k, $c->get($k));
    }

    /**
     * throws: IrfanTOOR\Container\ContainerException::class
     * message: Identifier can only be a non empty string.
     * b: Provider::badKeys()
     */
    function testRemoveException($b)
    {
        $c = new Container();
        $c->remove($b);
    }

    /**
     * throws: IrfanTOOR\Container\NotFoundException::class
     * message: No entry was found for {$k} identifier.
     * k: Provider::goodKeys()
     */
    function testRemoveNotFoundException($k)
    {
        $c = new Container();
        $c->remove($k);
    }

    function testRemove()
    {
        $init = Provider::goodInitArgs();
        $c = new Container($init);

        foreach ($init as $k => $v) {
            $this->assertTrue($c->has($k));
            $c->remove($k);
            $this->assertFalse($c->has($k));
        }
    }
}

class MockContainer extends Container
{
    public function getAdapter() {
        return $this->adapter;
    }
}
