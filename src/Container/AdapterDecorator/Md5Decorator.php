<?php

namespace IrfanTOOR\Container\AdapterDecorator;

class Md5Decorator extends AbstractDecorator
{	
	function set($id, $value=null) {
		if (is_array($id)) {
			foreach($id as $k=>$v) {
				$this->set($k, $v);
			}
		}
		else {
			$this->adapter->set($this->normalize($id), [$id, md5($value)]);
		}
	}
}
