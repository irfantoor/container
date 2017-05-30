<?php

namespace IrfanTOOR\Container\Adapter;

class FileAdapter extends AbstractAdapter
{
	protected $file;
	
	function __construct($file) {
		$init = [];
		$this->file = $file;
		
		if (!file_exists($file)) {
			file_put_contents($file, json_encode($init));
			$this->data = $init;
		} else {
			$this->data = json_decode(file_get_contents($file), 1);
		}
	}
	
	# For reducing disk operations a multiple set with array of elements is allowed
	function set($id, $value=null, $commit=true) {
		if (is_array($id)) {
			foreach ($id as $k=>$v)
				$this->set($k, $v, false);
		} 
		elseif (is_string($id)) {
			$this->data[$id] = $value;
		}
		if ($commit)
			file_put_contents($this->file, json_encode($this->data));
	}
	
	# For reducing disk operations a multiple set with array of elements is allowed
	function remove($id, $commit=true) {
		$removed = false;
		if (is_array($id)) {
			foreach ($id as $k)
				$removed = $removed || $this->remove($k, false);
			
			if ($removed)
				file_put_contents($this->file, json_encode($this->data));
		} 
		elseif ($this->has($id)) {
			unset($this->data[$id]);
			if ($commit)
				file_put_contents($this->file, json_encode($this->data));
			
			$removed = true;
		}
		return $removed;
	}
}
