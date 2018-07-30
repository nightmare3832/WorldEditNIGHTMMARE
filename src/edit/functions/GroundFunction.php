<?php

namespace edit\functions;

use edit\Vector;
use edit\functions\mask\Mask;

class GroundFunction implements LayerFunction{

	private $mask;
	private $function1;
	private $affected;

	public function __construct(Mask $mask, RegionFunction $function1){
		$this->mask = $mask;
		$this->function1 = $function1;
	}

	public function getMask() : Mask{
		return $this->mask;
	}

	public function setMask(Mask $mask){
		$this->mask = $mask;
	}

	public function getAffected(){
		return $this->affected;
	}

	public function isGround(Vector $position) : bool{
		return $this->mask->test($position);
	}

	public function apply(Vector $position, int $depth) : bool{
		if($depth == 0){
			if($this->function1->apply($position)){
				$this->affected++;
			}
		}

		return false;
	}

}