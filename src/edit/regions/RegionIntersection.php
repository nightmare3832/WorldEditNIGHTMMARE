<?php

namespace edit\regions;

class RegionIntersection{

	public $regions = [];

	public function __construct(array $regions){
		$this->regions = $regions;
	}

	public function getMinimumPoint() : Vector{
		$minimum = $this->regions[0]->getMinimumPoint();
		for($i = 1;$i < count($this->regions);$i++){
			$minimum = Vector::getMinimum($this->regions[$i]->getMinimumPoint(), $minimum);
		}
		return $minimum;
	}

	public function getMaximumPoint() : Vector{
		$maximum = $this->regions[0]->getMaximumPoint();
		for($i = 1;$i < count($this->regions);$i++){
			$maximum = Vector::getMaximum($this->regions[$i]->getMaximumPoint(), $maximum);
		}
		return $maximum;
	}

	public function expand(array $changes){
	}

	public function contract(array $changes){
	}

	public function contains(Vector $position) : bool{
		for($this->regions as $region){
			if($region->contains($position)){
				return true;
			}
		}

		return false;
	}
}