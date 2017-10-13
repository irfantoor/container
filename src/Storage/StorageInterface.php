<?php

namespace IrfanTOOR\Storage;

Interface StorageInterface
{
	# Connects to a storage
	# $connection	: connection array or ; separated string
	function connect($connection);
	
	# Initializes the container
	# $init			: array of data to be initialized
	# $adapter		: A IrfanTOOR\Container\Adapter for storing container
	# $flags		: Container::LOCKED or Container::NOCASE, default is none of these
	function init($init = [], $flags = 0);
	
	# Sets an $identifier and its Value pair
	# $id			: identifier or array of id, value pairs
	# $value		: value of identifier or null if the parameter id is an array 
	function set($id, $value = null);
	
	# Checks if the identifier is present in the container
	# $id			: identifier
	function has($id);
	
	# Gets the value associated with an identifier
	# $id			: identifier
	# $default		: to be returned if the identifier is not present in the container
	# returns		: the value associated with the identifier or the default value
	function get($id, $default = null);
	
	# Removes the value from identified by an identifier from the container
	# $id			: identifier
	function remove($id);
	
	# Clears the container by removing all of the contained values
	function clear();
	
	# Returns the container as an array collection
	function toArray();
}
