<?php

namespace IrfanTOOR\Container\AdapterDecorator;

use IrfanTOOR\Container\Exception;
use IrfanTOOR\Container\Adapter\AdapterInterface;

abstract class AbstractDecorator implements AdapterInterface 
{
	protected $adapter;
	
	function __construct(AdapterInterface $adapter, $init=[]) {
		$this->adapter = $adapter;
		
		foreach($init as $k=>$v) {
			$this->set($k, $v);
		}
	}
	
	function normalize($id) {
		return $id;
	}
	
	function get($id){
		return $this->adapter->get($this->normalize($id))[1];
	}
	
	function has($id){
		return $this->adapter->has($this->normalize($id));
	}
	
	function set($id, $value=null) {
		$a = [];
		if (is_array($id)) {			
			foreach ($id as $k=>$v) {
				$a[$this->normalize($k)] = [$k, $v];
			}
		}
		else {
			$a[$this->normalize($id)] = [$id, $value];
		}
		$this->adapter->set($a);
	}
	
	function remove($id){
		$a = [];
		if (is_array($id)) {			
			foreach ($id as $k) {
				$a[] = $this->normalize($k);
			}
		}
		else {
			$a[] = $this->normalize($id);
		}
		return $this->adapter->remove($a);
	}
	
	function toArray() {
		return $this->adapter->toArray();
	}
}
