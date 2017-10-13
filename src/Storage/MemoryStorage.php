<?php

namespace IrfanTOOR\Storage;

class MemoryStorage extends AbstractStorage
{
	protected 
		$data    = [],
		$keys    = [];
			
	# Constructs the container
	#
	# $connection   : connection string or array
	function __construct($connection = null)
	{
		parent::__construct($connection);
	}
		
	# Sets an $identifier and its Value pair
	function set($id, $value = null)
	{
		if ($this->locked)
			return;
			
		if (is_array($id)) {
			foreach ($id as $k => $v) {
				$this->set($k, $v);
			}
		}
		else {
			if (is_string($id)) {
				$this->remove($id);
				
				if ($this->nocase)
					$this->keys[strtolower($id)] = $id;
					
				$this->data[$id] = $value;
			}
		}
	}
	
	# Checks if the identifier is present in the container
	function has($id) 
	{
		if ($this->nocase)
			return array_key_exists(strtolower($id), $this->keys);
		
		return array_key_exists($id, $this->data);
	}
	
	# Gets the value associated with an identifier
	function get($id, $default = null) 
	{
		if (!$this->has($id))
			return $default;
			
		if (!$this->nocase)
			return $this->data[$id];
		
		return $this->data[$this->keys[strtolower($id)]];
	}
	
	# Removes the value from identified by an identifier from the container
	function remove($id) 
	{
		if ($this->locked)
			return false;
		
		if ($this->has($id)) {
			if ($this->nocase) {
				$key = $this->keys[strtolower($id)];
				unset($this->keys[strtolower($id)]);
				$id = $key;
			}
			
			unset($this->data[$id]);
		}
	}
	
	# Clears the container by removing all of the contained values
	function clear()
	{
		if ($this->locked)
			return false;

		if ($this->nocase) {
			unset($this->keys);
			$this->keys = [];
		}
		
		unset($this->data);
		$this->data = [];
	}
	
	# Returns the container as an array collection
	function toArray()
	{
		return $this->data;
	}
}
