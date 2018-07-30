<?php

namespace edit\jnbt;

class StringTag extends Tag{

	private $value;

	public function __construct(string $value){
		parent::__construct();
		$this->value = $value;
	}

	public function getValue(){
		return $this->value;
	}
}