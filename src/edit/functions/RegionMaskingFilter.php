<?php

namespace edit\functions;

use edit\Vector;
use edit\functions\mask\Mask;

class RegionMaskingFilter implements RegionFunction{

	private $function1;
	private $mask;

	public function __construct(Mask $mask, RegionFunction $function1){
		$this->mask = $mask;
		$this->function1 = $function1;
	}

	public function apply(Vector $position) : bool{
		return $this->mask->test($position) && $this->function1->apply($position);
	}
}