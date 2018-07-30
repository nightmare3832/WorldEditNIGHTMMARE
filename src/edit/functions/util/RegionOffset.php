<?php

namespace edit\functions\util;

use edit\Vector;
use edit\functions\RegionFunction;

class RegionOffset implements RegionFunction{

	private $offset;
	private $function1;

	public function __construct(Vector $offset, RegionFunction $function1){
		$this->setOffset($offset);
		$this->function1 = $function1;
	}

	public function getOffset() : Vector{
		return $this->offset;
	}

	public function setOffset(Vector $offset){
		$this->offset = $offset;
	}

	public function apply(Vector $position) : bool{
		return $this->function1->apply($position->add($this->offset));
	}
}