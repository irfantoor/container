<?php

namespace IrfanTOOR\Container\AdapterDecorator;

use IrfanTOOR\Container\Exception;

class ReadOnlyDecorator extends AbstractDecorator
{
	function set($id, $value=null) {
		throw new Exception("ReadOnly Decorator");
	}
	
	function remove($id){
		throw new Exception("ReadOnly Decorator");
	}
}
