<?php

/**
 * IrfanTOOR\Container\Processor\AbstractProcessor
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Processor;

Abstract class AbstractProcessor
{
    /**
     * Process the identifier before any operation
     *
     * @param string Identifier
     */
    public function processId(string $id): string
    {
        return $id;
    }

    /**
     * Process the entry before set operation
     *
     * @param mixed Identifier
     */
    public function preProcessEntry($entry)
    {
        return $entry;
    }

    /**
     * Process the entry after get operation
     *
     * @param mixed entry
     */
    public function postProcessEntry($entry)
    {
        return $entry;
    }
}
