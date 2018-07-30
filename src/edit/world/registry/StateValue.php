<?php

namespace edit\world\registry;

use edit\Vector;
use edit\blocks\BaseBlock;

interface StateValue{

	function isSet(BaseBlock $block) : bool;

	function set(BaseBlock $block) : bool;

	function getDirection() : ?Vector;

}