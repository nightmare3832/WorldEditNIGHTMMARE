<?php

namespace edit\functions\operation;

class Operations{

	public function __construct(){
	}

	public static function complete(Operation $op){
		while($op != null){
			$op = $op->resume(new RunContext());
		}
	}

	public static function completeLegacy(Operation $op){
		while($op != null){
			$op = $op->resume(new RunContext());
		}
	}
}