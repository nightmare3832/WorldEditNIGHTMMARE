<?php

namespace edit\command\tool\brush;

use edit\Vector;
use edit\EditSession;
use edit\functions\pattern\Pattern;

interface Brush{

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size);
}