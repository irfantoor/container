<?php

namespace IrfanTOOR\Container\Decorator;

use IrfanTOOR\Container\Exception;

class ReadOnlyDecorator extends AbstractDecorator
{
	protected $locked = false;
	function __construct($init=[], $adapter=null) {
		parent::__construct($init, $adapter);
		
		$this->locked = true;
	}
	
	function set($id, $value) {
		if ($this->locked)
			return;
			
		parent::set($id, $value);
	}
	
	function remove($id) {
		if ($this->locked)
			return;
			
		parent::remove($id);
	}
}
