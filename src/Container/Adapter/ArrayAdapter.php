<?php

/**
 * IrfanTOOR\Container\Adapter\ArrayAdapter
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Adapter;

use Exception;

class ArrayAdapter extends AbstractAdapter implements AdapterInterface
{
    /** @var array -- Data storage for container */
    protected $data = [];

    public function __construct()
    {
    }

    public function get(string $id)
    {
        return $this->has($id) ? $this->data[$id] : null;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->data);
    }

    public function set(string $id, $entry): bool
    {
        $this->data[$id] = $entry;
        return true;
    }

    public function remove(string $id): bool
    {
        if (!$this->has($id))
            return false;

        unset($this->data[$id]);
        return true;
    }
}
