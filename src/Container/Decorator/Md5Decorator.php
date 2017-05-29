<?php

namespace IrfanTOOR\Container\Decorator;

class Md5Decorator extends AbstractDecorator
{	
	function set($id, $value=null) {
		$this->adapter->set($id, md5($value));
	}
}
