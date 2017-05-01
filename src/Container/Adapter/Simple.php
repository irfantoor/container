<?php

namespace IrfanTOOR\Container\Adapter;

class Simple extends AbstractAdapter
{
	protected $data;
	
	function __construct($init=null) 
	{
		$this->data = $init;
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
		$this->data[$id] = $value;
	}
	
	public function remove($id)
	{
		if ($this->has($id))
			unset($this->data[$id]);
	}
	
	public function toArray() 
	{
		return $this->data;
	}
}
