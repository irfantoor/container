<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Adapter\AdapterInterface;

abstract class AbstractAdapter implements AdapterInterface 
{
	protected $data;
	
	function __construct($init=[])
	{
		$this->set($init);
	}
	
	function get($id)
	{
		return $this->data[$id];
	}
	
	function has($id) 
	{
		return array_key_exists($id, $this->data);
	}
	
	function set($id, $value=null)
	{
		if (is_array($id)) {
			foreach($id as $k=>$v) {
				$this->data[$k] = $v;
			}
		}
		else {
			$this->data[$id] = $value;
		}
	}
	
	function remove($id)
	{
		if (is_array($id)) {
			foreach($id as $k) {
				$this->remove($k);
			}
		}
		else {
			if ($this->has($id))
				unset($this->data[$id]);
		}
	}
		
	function toArray() 
	{
		return $this->data;
	}	
}
