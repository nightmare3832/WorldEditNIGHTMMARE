<?php

namespace edit\functions\mask;

class BoundedHeightMask extends AbstractMask{

	private final $minY;
	private final $maxY;

	public function __construct($minY, $maxY) {
		$this->minY = $minY;
		$this->maxY = $maxY;
	}

	public function test($vector) : bool{
		return $vector->getY() >= $this->minY && $vector->getY() <= $this->maxY;
	}

	public function toMask2D() : ?Mask2D{
		return null;
	}

}