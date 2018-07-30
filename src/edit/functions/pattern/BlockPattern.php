<?php

namespace edit\functions\pattern;

use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\Vector;

class BlockPattern extends AbstractPattern{

	private $block;

	public function __construct(BaseBlock $block){
		$this->setBlock($block);
	}

	public function getBlock() : BaseBlock{
		return $this->block;
	}

	public function setBlock(BaseBlock $block){
		$this->block = $block;
	}

	public function apply(Vector $position) : BaseBlock{
		return $this->block;
	}
}