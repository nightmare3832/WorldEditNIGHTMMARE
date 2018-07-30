<?php

namespace edit\extent\clipboard;

use edit\Vector;
use edit\regions\Region;
use edit\extent\Extent;

interface Clipboard extends Extent{

	function getRegion() : Region;

	function getDimensions() : Vector;

	function getOrigin() : Vector;

	function setOrigin(Vector $origin);

}