<?php

namespace edit\functions;

use edit\Vector;

interface RegionFunction{

	public function apply(Vector $position) : bool;

}