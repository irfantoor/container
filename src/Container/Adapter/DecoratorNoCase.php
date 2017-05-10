<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Exception;
use IrfanTOOR\Container\AdapterInterface;

class DecoratorNoCase extends AbstractAdapter
{
	private $adapter;
	
	function __construct(AdapterInterface $adapter, $init=[]) {
		$this->adapter = $adapter;
		
		foreach($init as $k=>$v) {
			$this->set($k, $v);
		}
	}
	
	private function normalize($id) {
		return strtolower($id);
	}
	
	public function get($id){
		return $this->adapter->get($this->normalize($id))["value"];
	}
	
	public function has($id){
		return $this->adapter->has($this->normalize($id));
	}
	
	public function set($id, $value) {
		$this->adapter->set($this->normalize($id), ["id"=>$id, "value"=>$value]);
	}
	
	public function remove($id){
		return $this->adapter->remove($this->normalize($id));
	}
	
	public function toArray(){
		$a = [];
		foreach($this->adapter->toArray() as $item) {
			$a[$item["id"]] = $item["value"];
		}
		return $a;
	}
}
