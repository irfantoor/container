<?php

namespace IrfanTOOR\Container\Decorator;

class MD5Decorator extends AbstractDecorator
{	
	function set($id, $value=null) {
		if (!is_string($value))
			$value = print_r($value, 1);
			
		$this->adapter->set($id, md5($value));
	}
}
