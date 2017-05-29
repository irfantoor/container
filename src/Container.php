<?php

namespace IrfanTOOR;

use Psr\Container\ContainerInterface;

use IrfanTOOR\Container\NotFoundException;
use IrfanTOOR\Container\ContainerException;
use IrfanTOOR\Container\Decorator\AbstractDecorator;
use IrfanTOOR\Container\Adapter\ArrayAdapter;

class Container extends AbstractDecorator implements ContainerInterface
{
	function __construct($init=[], $adapter_or_decorator=null) {
		$adapter_or_decorator = $adapter_or_decorator ? : new ArrayAdapter;
		parent::__construct($init, $adapter_or_decorator);
	}
	
    public function get($id, $default=null)
    {
    	if ($this->has($id)) 
    	{    	
			try {
				return $this->adapter->get($id);
			}
			catch(\Exception $e) {
				throw new ContainerException("Error while retrieving the entry");
			}
    	}
    	elseif ($default === null) {
    		throw new NotFoundException("No entry was found for **{$id}** identifier");
    	} else {
    		return $default;
    	}
    }
}
