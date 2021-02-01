<?php

/**
 * php version ??
 *
 * @author    Your Name <your_email@your_domain.com>
 * @copyright __year__ Your Name
 * ...
 */

namespace Your\Project;

use IrfanTOOR\Container\Adapter\{AbstractAdapter, AdapterInterface};

/**
 * This is an adapter template to write your own data adapter for container
 */
class AdapterTemplate extends AbstractAdapter implements AdapterInterface
{
    public function __construct()
    {
    }

    public function get(string $id)
    {
        # write your code to retrive entry and return
    }


    public function has(string $id): bool
    {
        # write your code to verify if the entry is present
    }

    public function set(string $id, $entry): bool
    {
        # write your code to set the entry
    }

    public function remove(string $id): bool
    {
        # write your code to remove the entry
    }
}
