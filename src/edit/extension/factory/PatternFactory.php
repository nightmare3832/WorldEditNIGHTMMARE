<?php

namespace edit\extension\factory;

use edit\internal\registry\AbstractFactory;

class PatternFactory extends AbstractFactory{

	public function __construct(){
		//$this->parsers[] = new HashTagPatternParser();
		$this->parsers[] = new SingleBlockPatternParser();
		$this->parsers[] = new RandomPatternParser();
	}

}