<?php

namespace edit\functions\block;

use edit\functions\RegionFunction;
use edit\functions\pattern\Pattern;
use edit\extent\Extent;
use edit\Vector;

class BlockReplace implements RegionFunction{

	private $extent;
	private $pattern;

	public function __construct(Extent $extent, Pattern $pattern){
		$this->extent = $extent;
		$this->pattern = $pattern;
	}

	public function apply(Vector $position) : bool{
		return $this->extent->setBlock($position, $this->pattern->apply($position));
	}
}