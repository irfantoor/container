<?php

require_once "Provider.php";

use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\ArrayAdapter;
use IrfanTOOR\Container\Processor\{ProcessorInterface, AbstractProcessor, HashIdProcessor};
use IrfanTOOR\Test;

class HashIdProcessorTest extends Test
{
    function testInstance()
    {
        $p = new HashIdProcessor();

        $this->assertInstanceOf(HashIdProcessor::class, $p);
        $this->assertInstanceOf(AbstractProcessor::class, $p);
        $this->assertImplements(ProcessorInterface::class, $p);
    }

    function testProcessId()
    {
        $c = new Container();
        $p = new HashIdProcessor();
        $c->addProcessor($p);
        $init = Provider::goodInitArgs();
        $c->init($init);

        $r = new ReflectionClass(Container::class);
        $p = $r->getProperty('adapter');
        $p->setAccessible(true);
        $a = $p->getValue($c);

        $r = new ReflectionClass(ArrayAdapter::class);
        $p = $r->getProperty('data');
        $p->setAccessible(true);
        $data = $p->getValue($a);

        foreach ($init as $k => $v)
        {
            $k = md5($k);
            $this->assertEquals($v, $data[$k]);
        }
    }
}
