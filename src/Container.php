<?php

namespace IrfanTOOR;

use Psr\Container\ContainerInterface;
use IrfanTOOR\Container\NotFoundException;
use IrfanTOOR\Container\ContainerException;
use IrfanTOOR\Container\AdapterInterface;

class Container implements ContainerInterface
{
	protected $adapter;
	
	function __construct(AdapterInterface $adapter) 
	{
		$this->adapter = $adapter;
	}
	
	function normalize($id) {
    	if (!is_string($id)) {
    		if (is_object($id)) {
    			if (method_exists($id, "toString")) {
    				$id = $id->toString($id);
    			} else {
    				return null;
    			}
    		} else {
    			$id = (string) $id;
    		}
    	}
    	
    	return $id;		
	}
	
    public function get($id, $default=null)
    {
    	if ($this->has($id)) 
    	{    	
			try {
				$result = $this->adapter->get($id);
			}
			catch(\Exception $e) {
				throw new ContainerException("Error while retrieving the entry");
			}
    		
    		return $result;
    	}
    	else {
    		if ($default === null)	
    			throw new NotFoundException("No entry was found for **{$id}** identifier");
    		else
    			return $default;
    	}
    }
    
    public function has($id){
		$id = $this->normalize($id);
    	
    	if (!$id || $id === "") {
    		return false;
    	}

    	return $this->adapter->has($id);    		
    }
    
    public function set($id, $value) {
    	$this->adapter->set($id, $value);
    }
    
    public function remove($id) {
    	$this->adapter->remove($id);
    }    
    
    public function toArray() {
    	return $this->adapter->toArray();
    }
}
