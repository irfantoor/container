<?php

namespace IrfanTOOR\Container;

Interface AdapterInterface 
{
	public function get($id);
	public function has($id);
	public function set($id, $value);
	public function remove($id);
	public function toArray();
}
