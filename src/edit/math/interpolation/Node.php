<?php

namespace edit\math\interpolation;

use edit\Vector;

class Node{

	private $position;

	private $tension;
	private $bias;
	private $continuity;

	public function __construct() {
		this(new Vector(0, 0, 0));
	}

	public function __construct(Node $other = null){
		if($other == null) $other = new Vector(0, 0, 0);
		if($other instanceof Node){
			$this->position = $other->position;

			$this->tension = $other->tension;
			$this->bias = $other->bias;
			$this->continuity = $other->continuity;
		}else{
			$this->position = $other;
		}
	}


	public function getPosition() : Vector{
		return $this->position;
	}

	public function setPosition(Vector $position){
		$this->position = $position;
	}

	public function getTension() : float{
		return $this->tension;
	}

	public function setTension(float $tension){
		$this->tension = $tension;
	}

	public function getBias() : float{
		return $this->bias;
	}

	public function setBias(float $bias){
		$this->bias = $bias;
	}

	public function getContinuity() : float{
		return $this->continuity;
	}

	public function setContinuity(float $continuity){
		$this->continuity = $continuity;
	}

}