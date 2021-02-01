<?php

require_once "Provider.php";

use IrfanTOOR\Container;
use IrfanTOOR\Container\Adapter\ArrayAdapter;
use IrfanTOOR\Container\Processor\{ProcessorInterface, AbstractProcessor};
use IrfanTOOR\Test;

class ProcessorTest extends Test
{
    function testInstance()
    {
        $p = new MockProcessor();

        $this->assertInstanceOf(AbstractProcessor::class, $p);
        $this->assertImplements(ProcessorInterface::class, $p);
    }

    function testProcessorOperations()
    {
        $c = new Container();
        $p = new MockProcessor();
        $c->addProcessor($p);
        $init = Provider::goodInitArgs();
        $c->init($init);

        $r = new ReflectionClass(Container::class);
        $p = $r->getProperty('adapter');
        $p->setAccessible(true);
        $s = $p->getValue($c);

        $r = new ReflectionClass(ArrayAdapter::class);
        $p = $r->getProperty('data');
        $p->setAccessible(true);
        $data = $p->getValue($s);

        # processId and preProcessEntry (set)
        foreach ($init as $k => $v)
        {
            if (!is_string($v))
                $v = print_r($v, true);

            $k = "__" . $k . "__";
            $v = "pre" . $v;

            $this->assertEquals($v, $data[$k]);
        }

        # processId and postProcessEntry (get)
        foreach ($init as $k => $v)
        {
            if (!is_string($v))
                $v = print_r($v, true);

            $this->assertEquals("pre" . $v . "post", $c->get($k));
        }
    }
}

class MockProcessor extends AbstractProcessor implements ProcessorInterface
{
    public function processId(string $id): string
    {
        return "__" . $id . "__";
    }

    public function preProcessEntry($entry)
    {
        if (is_string($entry))
            $entry = "pre" . $entry;
        else
            $entry = "pre" . print_r($entry, true);

        return $entry;
    }

    public function postProcessEntry($entry)
    {
        if (is_string($entry))
            $entry .= "post";

        return $entry;
    }
}
