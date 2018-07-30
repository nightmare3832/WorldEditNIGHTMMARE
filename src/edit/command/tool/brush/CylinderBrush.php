<?php

namespace edit\command\tool\brush;

use edit\Vector;
use edit\EditSession;
use edit\WorldVector;
use edit\functions\pattern\Pattern;

class CylinderBrush implements Brush{

	private $height;

	public function __construct(int $height){
		$this->height = $height;
	}

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size){
		$editSession->makeCylinder($position, $pattern, $size, $size, $this->height, true);
	}
}