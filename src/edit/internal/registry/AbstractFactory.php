<?php

namespace edit\internal\registry;

abstract class AbstractFactory{

	protected $parsers = [];

	public function __constract(){
	}

	public function parseFromInput(string $input){
		foreach($this->parsers as $parser){
			$match = $parser->parseFromInput($input);

			if($match != null){
				return $match;
			}
		}

		echo("error");
	}
}