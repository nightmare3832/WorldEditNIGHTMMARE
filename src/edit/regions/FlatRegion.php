<?php

namespace edit\regions;

use edit\util\UtilIterable;

interface FlatRegion extends Region{

	public function getMinimumY() : int;

	public function getMaximumY() : int;

	public function asFlatRegion() : array;
}
