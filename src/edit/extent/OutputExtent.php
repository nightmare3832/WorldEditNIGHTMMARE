<?php

namespace edit\extent;

use pocketmine\block\Block;

use edit\Vector;
use edit\functions\operation\Operation;

interface OutputExtent{

	function setBlock(Vector $position, $block) : bool;

	//biome

	function commit() : ?Operation;

}