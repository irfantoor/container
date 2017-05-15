<?php

namespace IrfanTOOR\Container\Decorator;

class NoCaseDecorator extends AbstractDecorator
{
	protected $adpater;
	
	function __construct($adapter, $init=[]) {
		$this->adapter = $adapter;
		foreach($init as $k=>$v)
			$this->set($k, $v);
	}
	
	function get($id, $default=null) {
		if ($this->has(strtolower($id))) {
			$result = $this->adapter->get(strtolower($id));
			return $result[1];
		}
		
		return $default;
	}
	
	function has($id) {
		return $this->adapter->has(strtolower($id));
	}
	
	function set($id, $value=null) {
		$this->adapter->set(strtolower($id), [$id, $value]);
	}
	
	function remove($id) {
		$this->adapter->remove(strtolower($id));
	}
	
	function toArray() {
		$data = [];
		foreach($this->adapter->toArray() as $k=>$v)
			$data[$v[0]] = $v[1];
			
		return $data;
	}
}
