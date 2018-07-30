<?php

namespace edit\functions\mask;

use pocketmine\block\Block;

use edit\extent\Extent;

class ExistingBlockMask extends AbstractExtentMask{

	public function __construct(Extent $extent){
		parent::__construct($extent);
	}

	public function test($vector) : bool{
		return $this->getExtent()->getBlock($vector)->getType() != Block::AIR;
	}

	public function toMask2D() : ?Mask2D{
		return null;
	}

}