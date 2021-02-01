<?php

/**
 * IrfanTOOR\Container\Container\Processor\HashIdProcessor
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Processor;

use Exception;

class HashIdProcessor extends AbstractProcessor implements ProcessorInterface
{
    public function processId(string $id): string
    {
        return md5($id);
    }
}
