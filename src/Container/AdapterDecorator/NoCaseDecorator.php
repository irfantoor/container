<?php

namespace IrfanTOOR\Container\AdapterDecorator;

class NoCaseDecorator extends AbstractDecorator
{
	function normalize($id) {
		return strtolower($id);
	}	
}
