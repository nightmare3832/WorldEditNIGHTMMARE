<?php

namespace edit\jnbt;

class IntTag extends Tag{

	private $value;

	public function __construct(int $value){
		parent::__construct();
		$this->value = $value;
	}

	public function getValue(){
		return $this->value;
	}
}