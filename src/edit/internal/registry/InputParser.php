<?php

namespace edit\internal\registry;

abstract class InputParser{

	public function __construct(){
	}

	public abstract function parseFromInput(string $input);

}