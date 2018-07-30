<?php

namespace edit\functions\mask;

use edit\regions\Region;

class RegionMask extends AbstractMask{

	private $region;

	public function __construct(Region $region) {
		$this->region = $region;
	}

	public function getRegion() : Region{
		return $this->region;
	}

	public function setRegion(Region $region){
		$this->region = $region;
	}

	public function test($vector) : bool{
		return $this->region->contains($vector);
	}

	public function toMask2D() : ?Mask2D{
		return null;
	}

}