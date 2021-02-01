<?php

/**
 * IrfanTOOR\Container\Adapter\AdapterInterface
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Adapter;

use Exception;

interface AdapterInterface
{
    /**
     * Finds an entry by its identifier and returns it.
     *
     * @param string Identifier
     * @return mixed The entry if found, otherwise return null
     */
    public function get(string $id);

    /**
     * Returns true if the an entry for the given identifier is present.
     *
     * @param string $id Identifier
     * @return bool Returns true if present, otherwise returns false
     */
    public function has(string $id): bool;

    /**
     * Sets an entry by its identifier.
     *
     * @param string $id    Identifier of the entry to set.
     * @param mixed  $entry Entry to be saved
     */
    public function set(string $id, $value): bool;

    /**
     * Removes an entry by its identifier.
     *
     * @param string $id    Identifier of the entry to be removed
     * @return bool Returns true if removed, otherwise returns false
     */
    public function remove(string $id): bool;
}
