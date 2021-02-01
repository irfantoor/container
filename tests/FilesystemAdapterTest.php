<?php

require_once "Provider.php";

use IrfanTOOR\Container\Adapter\{AdapterInterface, AbstractAdapter, FilesystemAdapter};
use IrfanTOOR\Test;

class FilesystemAdapterTest extends Test
{
    function testInstance()
    {
        $s = new FilesystemAdapter(dirname(__FILE__) . '/storage/');

        $this->assertInstanceOf(FilesystemAdapter::class, $s);
        $this->assertInstanceOf(AbstractAdapter::class, $s);
        $this->assertImplements(AdapterInterface::class, $s);
    }

    function testMixed()
    {
        $s = new FilesystemAdapter(dirname(__FILE__) . '/storage/');

        $init = Provider::goodInitArgs();

        foreach ($init as $k => $v) {
            $this->assertFalse($s->has($k));
            $this->assertFalse($s->remove($k));
            $this->assertTrue($s->set($k, $v));
            $this->assertTrue($s->has($k));

            if ($k === 'closure') {
                $this->assertEquals($v(), $s->get($k)());
            } else {
                $this->assertEquals($v, $s->get($k));
            }

            $this->assertTrue($s->remove($k));
            $this->assertFalse($s->has($k));
        }
    }
}
