<?php

namespace IrfanTOOR\Container\Adapter;

class FileAdapter extends AbstractAdapter
{
	protected $data;
	protected $file;
	
	function __construct($file, $init=[]) {
		$this->file = $file;
		
		if (!file_exists($file)) {
			file_put_contents($file, json_encode($init));
			$this->data = $init;
		} else {
			$this->data = json_decode(file_get_contents($file), 1);
			if ($init) {
				$this->data = array_merge($this->data, $init);
				file_put_contents($file, json_encode($this->data));
			}
		}
	}
		
	function set($id, $value=null) {
		$this->data[$id] = $value;
		file_put_contents($this->file, json_encode($this->data));
	}
	
	function remove($id) {
		if ($this->has($id)) {
			unset($this->data[$id]);
			file_put_contents($this->file, json_encode($this->data));
		}
	}
}
