<?php

namespace IrfanTOOR\Container\Adapter;

Interface AdapterInterface 
{
	# returns the value of the entity referred by $id
	function get($id);
	
	# checks if the container has an entity referred by $id
	function has($id);
	
	# stores/replaces an entity in the container;
	function set($id, $value);
	
	# removes the entity referred by the $id
	function remove($id);
	
	# returns the entities stored in the container as an array
	function toArray();
}
