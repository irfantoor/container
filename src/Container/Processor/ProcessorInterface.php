<?php

/**
 * IrfanTOOR\Container\Processor\StorageInterface
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Processor;

use Exception;

interface ProcessorInterface
{
    /**
     * Process the identifier before any operation
     *
     * @param string Identifier
     */
    public function processId(string $id): string;

    /**
     * Process the entry before set operation
     *
     * @param mixed Identifier
     */
    public function preProcessEntry($entry);

    /**
     * Process the entry after get operation
     *
     * @param mixed entry
     */
    public function postProcessEntry($entry);
}
