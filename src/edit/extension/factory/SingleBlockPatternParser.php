<?php

namespace edit\extension\factory;

use edit\internal\registry\InputParser;
use edit\functions\pattern\BlockPattern;
use edit\Main;

class SingleBlockPatternParser extends InputParser{

	public function __construct(){
	}

	public function parseFromInput(string $input){
		$items = explode(",", $input);

		if(count($items) == 1){
			return new BlockPattern(Main::getInstance()->getBlockFactory()->parseFromInput($items[0]));
		}else{
			return null;
		}
	}

}