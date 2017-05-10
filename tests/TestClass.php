<?php

class TestClass
{
	public $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function value(){
		return $this->value;
	}
}
