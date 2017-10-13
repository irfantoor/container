<?php

namespace IrfanTOOR\Storage;

use Exception;
use SQLite3;

class HybridStorage extends SqliteStorage
{
	protected
		$ds;	# directory storage

	# Constructs the container
	#
	# $init   : array of data to be initialized
	# $adapter: A IrfanTOOR\Container\Adapter for storing container
	# $flags  : Container::LOCKED or Container::NOCASE, default is none of these
	function __construct($connection)
	{
		parent::__construct($connection);
	}
	
	function connect($connection) {
		$this->connection = parent::parse_connection($connection);
		
		# Connect Directory Storage
		$this->ds = new DirectoryStorage($this->connection);
		$file = $this->connection['path'] . '/' . '.hybrid.sqlite';
		$this->connection['file'] = $file;
		
		# Connect the SqliteStorage
		parent::connect($this->connection);
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
				$uid = $this->ds->uid($id);
				parent::set($id, $uid);
				$this->ds->set($id, $value);
			}
		}
	}
	
	# Checks if the identifier is present in the container
	function has($id) 
	{
		return parent::has($id);
	}
	
	# Gets the value associated with an identifier
	function get($id, $default = null) 
	{
		return ($this->has($id)) ? $this->ds->get($id, $default) : $default;
	}
	
	# Removes the value from identified by an identifier from the container
	function remove($id) 
	{
		if ($this->locked)
			return false;
		
		parent::remove($id);
	}
	
	# Clears the container by removing all of the contained values
	function clear()
	{
		if ($this->locked)
			return false;
		
		parent::clear();
	}
	
	# Returns the container as an array collection
	function toArray($limit = 100)
	{
		$list = parent::toArray();
		$array = [];
		foreach ($list as $k => $v) {
			$array[$k] = $this->get($k);
		}
		
		return $array;
	}
}
