<?php

namespace edit\functions;

use edit\Vector;

class CombinedRegionFunction implements RegionFunction{

	public $functions = [];

	public function __construct(array $functions){
		$this->functions = $functions;
	}

	public function add(RegionFunction $functions){
		foreach($functions as $function1){
			$this->functions[] = $function1;
		}
	}

	public function apply(Vector $position) : bool{
		$ret = false;
		foreach($this->functions as $function1){
			if($function1->apply($position)){
				$ret = true;
			}
		}
		return $ret;
	}
}