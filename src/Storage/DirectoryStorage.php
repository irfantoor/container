<?php

namespace IrfanTOOR\Storage;

use Exception;
use SQLite3;

class DirectoryStorage extends AbstractStorage
{
	protected
		$path;

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
		$this->connection = $this->parse_connection($connection);
		
		if (!isset($this->connection['path']))
			throw new Exception('path not defined in the connection');
			
		$path = $this->connection['path'];
		if (!file_exists($path))
			throw new Exception('path ***' . $path . '*** does not exist');

		$this->path = $path;
			
		# todo -- manage to check locked
		$this->locked = 0;
		
		# todo -- manage to check nocase
		$this->nocase = 0;
	}
	
	function uid($id) 
	{
		return ($this->nocase) ? md5(strtolower($id)) : md5($id);
	}
	
	function file($id) 
	{
		return $this->path . '/' . $this->uid($id);
	}
	
	private function files()
	{
		$d = dir($this->path);
		$files = [];
		while (false !== ($e = $d->read())) {
			if (($e==".")||($e=="..")) continue;
		   $files[] = $e;
		}
		$d->close();
		return $files;	
	}	
	
	function read($file) 
	{
		$data = file_get_contents($file);
		
		$tag  = substr($data, 0, 5);
		$p = strpos($data, ':', 5);

		$id = substr($data, 5, $p - 5);
		$data = substr($data, $p + 1 );

		switch ($tag) {
			case 'json:':
				$data = json_decode($data, 1);
				break;
		
			case 'obje:':
				$data = unserialize($data);
				break;
		
			default:
		}
		
		return [$tag, $id, $data];
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
				# $this->remove($id);
				$nid = strtolower($id);
							
				if ($this->nocase)
					$uid = md5($nid);
				else
					$uid = md5($id);
		
				if (is_string($value)) {
					$tag = 'data:';
					$data = $value;
				}
				elseif (is_object($value)) {	
					$tag = 'obje:';
					$data = serialize($value);
				}
				else {
					$tag = 'json:';
					$data = json_encode($value);
				}
							
				file_put_contents($this->path . '/' . $uid, $tag . $id . ':' . $data);
			}
		}
	}
		
	# Checks if the identifier is present in the container
	function has($id) 
	{
		return file_exists($this->file($id));
	}
	
	# Gets the value associated with an identifier
	function get($id, $default = null) 
	{
		$file = $this->file($id);
		
		if (!is_file($file))
			return $default;
		
		try {
			return $this->read($file)[2];
		}
		catch (Exception $e) {
		 	return $default;
		}
	}
	
	# Removes the value from identified by an identifier from the container
	function remove($id)
	{
		if ($this->locked)
			return false;
			
		$file = $this->file($id);
		
		if (is_file($file))
			unlink($file);
	}
		
	# Clears the container by removing all of the contained values
	function clear()
	{
		if ($this->locked)
			return false;
			
		foreach($this->files() as $file) {
			unlink($this->path . '/' . $file);
		}
	}
	
	# Returns the container as an array collection
	function toArray()
	{
		$array = [];
		foreach($this->files() as $file) {
			$data = $this->read($this->path . '/' . $file);
			$array[$data[1]] = $data[2];
		}

		return $array;
	}
}
