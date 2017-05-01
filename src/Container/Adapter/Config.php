<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Exception;

class Config extends AbstractAdapter
{
	protected $data;
	
	function __construct($file) 
	{
		if (!file_exists($file))
			throw new Exception("file ***{$file}*** not found");
		
		$this->data = json_decode(file_get_contents($file), 1);
	}
	
	function get($id) 
	{
		return $this->data[$id];
	}
	
	function has($id) 
	{
		return array_key_exists($id, $this->data);
	}
	
	public function set($id, $value)
	{
		throw new Exception("Config container is a readonly container");
	}
	
	public function remove($id)
	{
		throw new Exception("Config container is a readonly container");
	}		
	
	public function toArray() 
	{
		return $this->data;
	}
}
