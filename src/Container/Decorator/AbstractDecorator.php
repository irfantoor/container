<?php

namespace IrfanTOOR\Container\Decorator;

use IrfanTOOR\Container\Adapter\AdapterInterface;
use IrfanTOOR\Container\Adapter\ArrayAdapter;
use IrfanTOOR\Container\Adapter\FileAdapter;

abstract class AbstractDecorator implements AdapterInterface 
{
	protected $adapter;
	
	function __construct($init=[]) {
		if (is_array($init)) {
			$this->adapter = new ArrayAdapter;
			foreach($init as $k=>$v)
				$this->set($k, $v);
		}
		elseif (is_string($init)) {
			$file = $init;
			$this->adapter = new FileAdapter($file);
		}
		else {
			$this->adapter = $init ?: new ArrayAdapter;		
		}
	}
		
	function get($id, $default=null){
		return $this->adapter->get($id, $default);
	}
	
	function has($id){
		return $this->adapter->has($id);
	}
	
	function set($id, $value=null) {
		if (is_array($id)) {
			foreach ($id as $k=>$v)
				$this->set($k, $v);
		}
		else {	
			$this->adapter->set($id, $value);
		}
	}
	
	function remove($id){
		if (is_array($id)) {
			foreach ($id as $k)
				$this->remove($k);
		}
		else {
			$this->adapter->remove($id);
		}
	}
	
	function toArray() {
		return $this->adapter->toArray();
	}
}
