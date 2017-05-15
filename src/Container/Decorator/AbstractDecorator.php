<?php

namespace IrfanTOOR\Container\Decorator;

use IrfanTOOR\Container\Adapter\AdapterInterface;

abstract class AbstractDecorator implements AdapterInterface 
{
	protected $adapter;
	
	function __construct(AdapterInterface $adapter, $init=[]) {
		$this->adapter = $adapter;
		
		foreach($init as $k=>$v)
			$this->set($k, $v);
	}
	
	function get($id, $default=null){
		return $this->adapter->get($id, $default);
	}
	
	function has($id){
		return $this->adapter->has($id);
	}
	
	function set($id, $value=null) {
		$this->adapter->set($id, $value);
	}
	
	function remove($id){
		$this->adapter->remove($id);
	}
	
	function toArray() {
		return $this->adapter->toArray();
	}
}
