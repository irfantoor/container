<?php

/**
 * IrfanTOOR\Container\Adapter\FilesystemAdapter
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container\Adapter;

use Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Opis\Closure\SerializableClosure;
use function Opis\Closure\serialize;
use function Opis\Closure\unserialize;

class FilesystemAdapter extends AbstractAdapter implements AdapterInterface
{
    /** @var Filesystem */
    protected $fs;

    public function __construct(string $root)
    {
        $local = new LocalFilesystemAdapter($root);
        $this->fs = new Filesystem($local);
    }

    public function get(string $id)
    {
        if (!$this->has($id))
            return null;

        $data = $this->fs->read($id);
        return(unserialize($data));
    }

    public function has(string $id): bool
    {
        return $this->fs->fileExists($id);
    }

    public function set(string $id, $entry): bool
    {
        $entry = serialize($entry);

        if ($this->has($id))
            $this->fs->update($id, $entry);
        else
            $this->fs->write($id, $entry);

        return true;
    }

    public function remove(string $id): bool
    {
        if (!$this->has($id))
            return false;

        $this->fs->delete($id);
        return true;
    }
}
