<?php

/**
 * IrfanTOOR\Container\AbstractContainer
 * php version 7.3
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2021 Irfan TOOR
 */

namespace IrfanTOOR\Container;

use ArrayAccess;
use Exception;
use IrfanTOOR\Container\{NotFoundException, ContainerException};
use IrfanTOOR\Container\Adapter\{AdapterInterface, ArrayAdapter};
use IrfanTOOR\Container\Processor\ProcessorInterface;
use Psr\Container\ContainerInterface;
use Throwable;

/**
 * Abstract Container
 */
abstract class AbstractContainer implements ArrayAccess
{
    /** @var AdapterInterface */
    protected $adapter;

    /**
     * Processors of idenntifier or entry be it pre or post
     *
     * @var array
     */
    protected $processors  = [];

    /** @var Extensions */
    protected $extensions = [];

    /**
     * Initializes the storage
     *
     * @param AdapterInterface $adapter
     */
    public function initAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Initializes the container
     *
     * @param array $init Associative array [string $id => $entry, ...]
     * @throws ContainerException Identifier can only be a non empty string.
     */
    public function init(array $init)
    {
        foreach ($init as $id => $entry)
            $this->set($id, $entry);
    }

    # =========================================================================== #
    # Validations
    # =========================================================================== #

    /**
     * Verifies if the identifier is valid
     *
     * @param mixed $id
     * @return bool Returns true if valid, false otherwise
     */
    protected function isValid($id): bool
    {
        return (is_string($id) && $id !== '');
    }

    /**
     * Validates if the identifier is valid
     *
     * @param mixed $id
     * @throws ContainerException Identifier can only be a non empty string.
     */
    protected function validate($id)
    {
        if (!$this->isValid($id))
            throw new ContainerException("Identifier can only be a non empty string.");
    }

    /**
     * Asserts if the container has an entry
     *
     * @param mixed $id
     * @throws NotFoundException No entry was found for $id identifier.
     */
    protected function assertHas($id)
    {
        if (!$this->has($id))
            throw new NotFoundException("No entry was found for $id identifier.");
    }

    # =========================================================================== #
    # Container methods to process its entries
    # =========================================================================== #

    /**
     * Sets an entry in the container
     * @param mixed $id
     * @throws ContainerException Identifier can only be a non empty string.
     * @param mixed $entry
     */
    public function set($id, $entry): bool
    {
        $this->validate($id);

        try {
            return $this->setItem($id, $entry);
        } catch (Throwable $e) {
            throw new ContainerException("Error while setting the entry.");
        }
    }

    /**
     * Removes an entry from the container
     *
     * @param mixed $id
     * @throws ContainerException Identifier can only be a non empty string.
     * @throws NotFoundException No entry was found for $id identifier.
     * @returns bool Returns true if successful
     */
    public function remove($id): bool
    {
        $this->validate($id);
        $this->assertHas($id);

        try {
            return $this->removeItem($id);
        } catch (Throwable $e) {
            throw new ContainerException("Error while removing the entry.");
        }
    }

    # =========================================================================== #
    # Apply processors to identifier or entry, before or after the operation
    # =========================================================================== #

    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
    }

    public function getItem(string $id)
    {
        foreach ($this->processors as $p)
            $id = $p->processId($id);

        $entry = $this->adapter->get($id);

        foreach ($this->processors as $p)
            $entry = $p->postProcessEntry($entry);

        return $entry;
    }

    public function hasItem(string $id)
    {
        foreach ($this->processors as $p)
            $id = $p->processId($id);

        return $this->adapter->has($id);
    }

    public function setItem(string $id, $entry)
    {
        foreach ($this->processors as $p)
        {
            $id = $p->processId($id);
            $entry = $p->preProcessEntry($entry);
        }

        return $this->adapter->set($id, $entry);
    }

    public function removeItem(string $id)
    {
        foreach ($this->processors as $p)
            $id = $p->processId($id);

        return $this->adapter->remove($id);
    }

    # =========================================================================== #
    # Extensions
    # Note: any class can be addded as an extension, all of the functions of the
    # class can be called directly form class, provided
    # =========================================================================== #

    /**
     * Adds a class to container as an extension
     */
    public function addExtension(string $name, $class)
    {
        if (!is_object($class))
            throw new ContainerException("Only objects can be added as extensions.");

        $this->extensions[$name] = $class;
    }

    /**
     * Helps calling the method from the extensions
     */
    public function __call($method, $args)
    {
        foreach ($this->extensions as $name => $ext) {
            if (method_exists($ext, $method))
                return call_user_func_array([$ext, $method], $args);
        }
    }

    /**
     * Forcing a call to a method from a specific extension
     *
     * @param string $name   Name of the extension, used while adding
     * @param string $mathod Name of the method to call
     * @param array  $args   Array of arguments to be passed to the method
     */
    public function call(string $name, string $method, array $args = [])
    {
        if (!isset($this->extensions[$name]))
            throw new ContainerException("Extension $name, is not present.");
        
        return call_user_func_array([$this->extensions[$name], $method], $args);
    }

    # =========================================================================== #
    # Array access.
    # e.g. $c['hello'] = 'world!', isset($c['hello']), echo $c['hello'],
    # =========================================================================== #

    /**
     * e.g. $c['greeting'] = new Greeting('Hello World!');
     */
    public function offsetSet($id, $entry): bool
    {
        return $this->set($id, $entry);
    }

    /**
     * e.g. isset($c['greeting']);
     */
    public function offsetExists($id): bool
    {
        return $this->has($id);
    }

    /**
     * e.g. $greeting = $c['greeting'];
     */
    public function offsetGet($id)
    {
        return $this->get($id);
    }

    /**
     * e.g. unset($c['greeting']);
     */
    public function offsetUnset($id): bool
    {
        return $this->remove($id);
    }
}
