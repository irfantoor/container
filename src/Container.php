<?php
/**
 * IrfanTOOR\Container
 *
 * @author    Irfan TOOR <email@irfantoor.com>
 * @copyright 2017 Irfan TOOR
 * @license   https://github.com/irfantoor/container/blob/master/LICENSE (MIT License)
 * @link      https://github.com/irfantoor/container/src/Container.php
 */

namespace IrfanTOOR;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception as BaseException;
use IteratorAggregate;
use IrfanTOOR\Collection;
use IrfanTOOR\Container\ContainerException;
use IrfanTOOR\Container\NotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Container that exposes methods to read its entries.
 */
class Container implements
    ContainerInterface,
    ArrayAccess,
    Countable,
    IteratorAggregate
{
    const NAME        = "Irfan's Container";
    const DESCRIPTION = "An abstraction for many types of containers with a single API.";
    const VERSION     = "1.0.4";

    /**
     * Collection to store data
     *
     * @var Collection Object
     */
    protected $collection;

    /**
     * keeps track of frozen functions
     *
     * @var array
     */
    protected $frozen;

    /**
     * keeps track of factories
     *
     * @var array
     */
    protected $factory;

    /**
     * Constructs the container
     *
     * @param array $init array of initial values
     */
    public function __construct($init = [])
    {
        $this->collection = new Collection($init);
    }

    /**
     * Set Multiple key, values pairs
     *
     * @param Array key value pairs.
     *
     * @return nothing
     */
    public function setMultiple($data)
    {
        foreach ($data as $k=>$v) {
            $this->set($k, $v);
        }
    }

    /**
     * Sets the container values
     *
     * @param string $id    Identifier of the entry.
     * @param mixed  $value Entry to store.
     *
     * @return nothing
     */
    public function set($id, $value=null)
    {
        if (!is_string($id)) {
            throw new ContainerException('id must be a string', 1);
        } elseif (isset($this->frozen[$id])) {
            throw new ContainerException('A service started with id: ' . $id);
        } elseif (isset($this->factory[$id])) {
            throw new ContainerException('A factory exists with id: '. $id);
        } else {
            $this->collection->set($id, $value);
        }
    }

    /**
     * Saves a callable service
     *
     * @param string $id    Identifier of the entry.
     * @param mixed  $value Factory entry to store.
     *
     * @return nothing
     */
    public function factory($id, $value)
    {
        // $this->collection[$id] = $this->collection->factory($value);
        if (isset($this->frozen[$id])) {
            throw new ContainerException('A service started with the same name', 1);
        }

        $this->collection->set($id, $value);
        $this->factory[$id] = true;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!is_string($id)) {
            throw new ContainerException('id must be a string', 1);
        }

        if (!$this->has($id)) {
            throw new NotFoundException('No entry was found for **' . $id . '**');
        }

        try {
            $value = $this->collection->get($id);

            if (isset($this->frozen[$id])) {
                return $value;
            }

            if (method_exists($value, '__invoke')) {
                $value = $value();

                if (!isset($this->factory[$id])) {
                    $this->collection->set($id, $value);
                    $this->frozen[$id] = true;
                }
            }

            return $value;

        } catch(BaseException $e) {
            throw new ContainerException('misconfiguration of container', 1);
        }
    }

    /**
     * Returns true if the container can return an entry for the given id.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        if (!is_string($id)) {
            return false;
        }

        try {
            return $this->collection->has($id);
        } catch (BaseException $e) {
            return false;
        }
    }

    /**
     * Removes the value from identified by an identifier from the container
     *
     * @param string $id identifier
     *
     * @return bool true if successful in removing, false otherwise
     */
    public function remove($id)
    {
        unset($this->collection[$id]);
        unset($this->frozen[$id]);
        unset($this->factory[$id]);
    }

    /**
     * Returns all the items in the container as raw array
     *
     * @return array The container's raw data
     */
    public function toArray()
    {
        return $this->collection->toArray();
    }

    /**
     * Get container keys
     *
     * @return array The container's raw data keys
     */
    public function keys()
    {
        return $this->collection->keys();
    }

    // =========================================================================
    // ArrayAccess interface
    // =========================================================================

    /**
     * Set collection item
     *
     * @param String $id    The data key
     * @param Mixed  $value The data value
     *
     * @return boolval true if found, false otherwise
     */
    public function offsetSet($id, $value)
    {
        return $this->set($id, $value);
    }

    /**
     * Does this collection have a given key?
     *
     * @param String $id The data key
     *
     * @return boolval true if found, false otherwise
     */
    public function offsetExists($id)
    {
        return $this->has($id);
    }

    /**
     * Get collection item for key
     *
     * @param String $id The data key
     *
     * @return Mixed The key's value, or the default value
     */
    public function offsetGet($id)
    {
        return $this->get($id, null);
    }

    /**
     * Remove item from collection
     *
     * @param String $id The data key
     *
     * @return boolval true if successful, false otherwise
     */
    public function offsetUnset($id)
    {
        return $this->remove($id);
    }

    // =========================================================================
    // Countable interface
    // =========================================================================

    /**
     * Get number of items in collection
     *
     * @return Int
     */
    public function count()
    {
        return $this->collection->count();
    }

    // =========================================================================
    // IteratorAggregate interface
    // =========================================================================

    /**
     * Get collection iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->collection->getIterator());
    }
}
