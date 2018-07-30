<?php

namespace edit\command\tool\brush;

use edit\Vector;
use edit\EditSession;
use edit\WorldVector;
use edit\regions\CuboidRegion;
use edit\math\convolution\HeightMap;
use edit\math\convolution\HeightMapFilter;
use edit\math\convolution\GaussianKernel;
use edit\functions\pattern\Pattern;

class SmoothBrush implements Brush{

	private $iterations;
	private $naturalOnly;

	public function __construct(int $iterations, bool $naturalOnly = false){
		$this->iterations = $iterations;
		$this->naturalOnly = $naturalOnly;
	}

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size){
		$min = new WorldVector($editSession->getWorld(), $position->subtract($size, $size, $size));
		$max = $position->add($size, $size + 10, $size);
		$region = new CuboidRegion($min, $max);
		$heightMap = new HeightMap($editSession, $region, $this->naturalOnly);
		$filter = new HeightMapFilter(new GaussianKernel(5, 1.0));
		$heightMap->applyFilter($filter, $this->iterations);
	}
}