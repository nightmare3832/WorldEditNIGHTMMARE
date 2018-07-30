<?php

namespace edit\functions;

use edit\Vector;

interface LayerFunction{

	public function isGround(Vector $position) : bool;

	public function apply(Vector $position, int $depth) : bool;

}