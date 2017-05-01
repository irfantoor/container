<?php

namespace IrfanTOOR;

use Psr\Container\ContainerInterface;
use IrfanTOOR\Container\AdapterInterface;

class ContainerCI extends Container implements ContainerInterface
{
	protected $keys;
	
	function __construct(AdapterInterface $adapter) 
	{
		parent::__construct($adapter);
		foreach($this->adapter->toArray() as $k=>$v) {
			$this->keys[strtolower($k)] = $k;
		}
	}
		
    public function get($id, $default=null)
    {
    	$key = strtolower($this->normalize($id));
    	$key = isset($this->keys[$key]) ? $this->keys[$key] : $id;
    	
    	return parent::get($key, $default);
    }
    
    public function has($id){
    	return isset($this->keys[strtolower($this->normalize($id))]) ? true : false;
    }
    
    public function set($id, $value) {
		$key = strtolower($this->normalize($id));
		
		if (isset($this->keys[$key]))
			$this->adapter->remove($this->keys[$key]);
		
		$this->keys[$key] = $id;
    	parent::set($id, $value);
    }
    
    public function remove($id) {    	
		$key = strtolower($this->normalize($id));
		if (isset($this->keys[$key])) {
			parent::remove($this->keys[$key]);
			unset($this->keys[$key]);
		}    
    }    
}
