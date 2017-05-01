<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Exception;

class File extends AbstractAdapter
{
	protected $file;
	protected $data;
	
	function __construct($file, $create_file=false) 
	{
		if (!file_exists($file)) {
			if ($create_file)
				file_put_contents($file, json_encode([]));
			else
				throw new Exception("file ***{$file}*** not found");
		}
		$this->file = $file;
		$this->data = json_decode(file_get_contents($file), 1);
	}
	
	function get($id) 
	{
		return $this->data[$id];
	}
	
	function has($id) 
	{
		return array_key_exists($id, $this->data);
	}
	
	public function set($id, $value)
	{
		$this->data[$id] = $value;
		file_put_contents($this->file, json_encode($this->data));
	}
	
	public function remove($id)
	{
		if ($this->has($id))
			unset($this->data[$id]);
		file_put_contents($this->file, json_encode($this->data));
	}	
	
	public function toArray() 
	{
		return $this->data;
	}
}
