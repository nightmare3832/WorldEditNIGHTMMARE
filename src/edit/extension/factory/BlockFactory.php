<?php

namespace edit\extension\factory;

use edit\internal\registry\AbstractFactory;

class BlockFactory extends AbstractFactory{

	public function __construct(){
		$this->parsers[] = new DefaultBlockParser();
	}

}