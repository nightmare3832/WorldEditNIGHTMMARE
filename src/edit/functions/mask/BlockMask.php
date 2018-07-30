<?php

namespace edit\functions\mask;

use edit\extent\Extent;

class BlockMask extends AbstractExtentMask{

	private $blocks;

	public function __construct(Extent $extent, array $blocks){
		parent::__construct($extent);
		$this->blocks = $blocks;
	}

	public function add(array $blocks){
		foreach($blocks as $block){
			$this->blocks[] = $block;
		}
	}

	public function getBlocks() : array{
		return $this->blocks;
	}

	public function test($vector) : bool{
		$block = $this->getExtent()->getBlock($vector);
		foreach($this->blocks as $b){
			if($block->getData() == -1){
				if($block->getType() == $b->getType()) return true;
			}else{
				if($block->getType() == $b->getType() && $block->getData() == $b->getData()) return true;
			}
		}
		return false;
	}

	public function toMask2D() : ?Mask2D{
		return null;
	}
}