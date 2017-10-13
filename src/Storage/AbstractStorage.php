<?php

namespace IrfanTOOR\Storage;

use IrfanTOOR\Storage\StorageInterface;

class AbstractStorage implements StorageInterface
{
	const
		LOCKED      = 1,
		NOCASE      = 2;
		
	protected 
		$connection = [],
		$locked     = 0,   # container is not locked
		$nocase     = 0;	# case sensitive
	
	function __construct($connection = null)
	{
		$this->connect($connection);
	}
	
	function parse_connection($connection) {
		if (is_string($connection)) {
			$c = explode(';', $connection);
			$connection = [];
			foreach($c as $segment) {
				list($k, $v) = explode('=', $segment);
				$connection[trim($k)] = trim($v);
			}
		}
		
		if (!is_array($connection))
			$connection = [];	
		
		return $connection;
	}

	function connect($connection) {}
	
	# Initializes the container
	#
	# $init   : array of data to be initialized
	# $adapter: A IrfanTOOR\Container\Adapter for storing container
	# $flags  : Container::LOCKED or Container::NOCASE, default is none of these
	function init($init = [], $flags = 0)
	{
		$this->nocase  = ($flags & self::NOCASE) > 0 ? 1 : 0;
		
		# initialize data
		$this->set($init);
		
		$this->locked = ($flags & self::LOCKED) > 0 ? 1 : 0;
	}
	
	function set($id, $value = null) {}
	
	function has($id) {}
	
	function get($id, $default = null) {}
	
	function remove($id) {}
	
	function clear() {}
	
	function toArray() {}
}
