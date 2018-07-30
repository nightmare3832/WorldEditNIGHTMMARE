<?php

namespace edit\functions\mask;

use edit\extent\Extent;

abstract class AbstractExtentMask implements Mask{

	private $extent;

	public function __construct(Extent $extent){
		$this->setExtent($extent);
	}

	public function getExtent() : Extent{
		return $this->extent;
	}

	public function setExtent(Extent $extent){
		$this->extent = $extent;
	}
}