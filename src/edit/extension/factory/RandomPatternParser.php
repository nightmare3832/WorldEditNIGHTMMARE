<?php

namespace edit\extension\factory;

use edit\internal\registry\InputParser;
use edit\functions\pattern\BlockPattern;
use edit\functions\pattern\RandomPattern;
use edit\Main;

class RandomPatternParser extends InputParser{

	public function __construct(){
	}

	public function parseFromInput(string $input){
		$blockRegistry = Main::getInstance()->getBlockFactory();
		$randomPattern = new RandomPattern();

		foreach(explode(",", $input) as $token){
			if(count(explode("%", $token)) == 2){
				$p = explode("%", $token);

				if(count($p) < 2){
					//"Missing the type after the % symbol for '" + input + "'");
					echo("error");
				}else{
					$chance = $p[0];
					$block = $blockRegistry->parseFromInput($p[1]);
				}
			}else{
				$chance = 1;
				$block = $blockRegistry->parseFromInput($token);
			}

			$randomPattern->add(new BlockPattern($block), $chance);
		}

		return $randomPattern;
	}

}