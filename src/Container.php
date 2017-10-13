<?php

namespace IrfanTOOR;

use IrfanTOOR\Storage\MemoryStorage;

class Container
{
	const
		LOCKED   = 1,
		NOCASE   = 2;

	protected
		$storage = null;
	
	/**
	 * Constructs the container
	 *
	 * $init   : array of data to be initialized
	 * $storage: Storage name as string e.g. 'MemoryStorage' or 'FileStorage'
	 * $flags  : Container::LOCKED or Container::NOCASE, default is none of these
	 */
	function __construct($init = [], $storage = null, $flags = 0)
	{
		if (!$storage)
			$storage = new MemoryStorage();
			
		$this->storage = $storage;
		$this->storage->init($init, $flags);
	}
	
	/**
	 * Sets an $identifier and its Value pair
	 * # $id   : identifier or array of id, value pairs
	 * # $value: value of identifier or null if the parameter id is an array 
	 */
	function set($id, $value = null)
	{
		$this->storage->set($id, $value);
	}
	
	/**
	 * Checks if the identifier is present in the container
	 * $id   : identifier
	 */
	function has($id)
	{
		return $this->storage->has($id);
	}
	
	/**
	 * Gets the value associated with an identifier
	 * $id     : identifier
	 * $default: to be returned if the identifier is not present in the container
	 * returns : the value associated with the identifier or the default value
	 */
	function get($id, $default = null) 
	{
		return $this->storage->get($id, $default);
	}
	
	/**
	 * Removes the value from identified by an identifier from the container
	 * $id: identifier
	 */
	function remove($id) 
	{
		$this->storage->remove($id);
	}
	
	/**
	 * Clears the container by removing all of the contained values
	 */
	function clear()
	{
		$this->storage->clear();
	}
	
	/**
	 * Returns the container as an array collection
	 */
	function toArray()
	{
		return $this->storage->toArray();
	}
}
