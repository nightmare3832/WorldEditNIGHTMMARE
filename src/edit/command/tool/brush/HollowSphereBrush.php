<?php

namespace edit\command\tool\brush;

use edit\Vector;
use edit\EditSession;
use edit\WorldVector;
use edit\regions\CuboidRegion;
use edit\functions\pattern\Pattern;

class HollowSphereBrush implements Brush{

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size){
		$editSession->makeSphere($position, $pattern, $size, $size, $size, false);
	}
}