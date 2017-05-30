<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Adapter\AdapterInterface;

abstract class AbstractAdapter implements AdapterInterface 
{
	protected $data = [];
		
	function __construct($init=[])
	{
		foreach($init as $k=>$v)
			$this->set($k, $v);
	}
	
    function get($id, $default=null)
    {
		if ($this->has($id))
			return $this->data[$id];
		
    	return $default;
    }
    
    function has($id)
    {
    	if (is_string($id))
    		return array_key_exists($id, $this->data);
    	
    	return false;
    }
    
    function set($id, $value=null) 
    {
		if (is_array($id)) {
			foreach ($id as $k=>$v)
				$this->set($k, $v);
		}
		elseif (is_string($id)) {
		    $this->data[$id] = $value;
	    }
    }
    
    function remove($id) {
		if (is_array($id)) {
			foreach ($id as $k)
				$this->remove($k);
		}
		elseif ($this->has($id)) {
			unset($this->data[$id]);
		}
    }
    
    function toArray() {
    	return $this->data;
    }	
}
