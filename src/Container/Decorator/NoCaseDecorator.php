<?php

namespace IrfanTOOR\Container\Decorator;

class NoCaseDecorator extends AbstractDecorator
{
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
		if (is_array($id)) {
			foreach ($id as $k=>$v)
				$this->set($k, $v);
		} 
		elseif (is_string($id)) {
			$this->adapter->set(strtolower($id), [$id, $value]);
		}
	}
	
	function remove($id) {
		if (is_array($id)) {
			foreach ($id as $k)
				$this->remove($k);
		}
		elseif (is_string($id)) {
			$this->adapter->remove(strtolower($id));
		}
	}
	
	function toArray() {
		$data = [];
		foreach($this->adapter->toArray() as $k=>$v)
			$data[$v[0]] = $v[1];
			
		return $data;
	}
}
