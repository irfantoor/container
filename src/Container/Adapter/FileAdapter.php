<?php

namespace IrfanTOOR\Container\Adapter;

use IrfanTOOR\Container\Exception;

class FileAdapter extends AbstractAdapter
{
	protected $file;
	
	function __construct($file, $create_file=false) 
	{
		if (!file_exists($file)) {
			if ($create_file)
				file_put_contents($file, json_encode([]));
			else
				throw new Exception("file ***{$file}*** not found");
		} 
		else {
			$this->data = json_decode(file_get_contents($file), 1);
		}
		$this->file = $file;
	}
	
	function set($id, $value=null)
	{
		parent::set($id, $value);
		file_put_contents($this->file, json_encode($this->data));
	}
	
	function remove($id)
	{
		parent::remove($id);
		file_put_contents($this->file, json_encode($this->data));
	}	
}
