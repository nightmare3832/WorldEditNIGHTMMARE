<?php

namespace edit\command\util;

class FlagChecker{

	public static function check(array $args) : array{
		$flags = [];
		$k = null;

		foreach($args as $key => $arg){
			if(substr($arg, 0, 1) == "-"){
				$str = substr($arg, 1);
				$flags = str_split($str);
				$k = $key;
			}
		}
		if($k != null) unset($args[$k]);
		array_values($args);

		return [$args, $flags];
	}
}