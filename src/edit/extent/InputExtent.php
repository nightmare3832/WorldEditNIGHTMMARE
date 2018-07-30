<?php

namespace edit\extent;

use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\Vector;

interface InputExtent{

	function getBlock(Vector $position) : BaseBlock;

	//biome

}