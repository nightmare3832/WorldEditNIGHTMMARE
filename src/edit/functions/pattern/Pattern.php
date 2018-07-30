<?php

namespace edit\functions\pattern;

use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\Vector;

interface Pattern{

	function apply(Vector $position) : BaseBlock;

}