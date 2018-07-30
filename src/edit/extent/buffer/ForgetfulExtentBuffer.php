<?php

namespace edit\extent\buffer;

use edit\BlockVector;
use edit\Vector;
use edit\blocks\BaseBlock;
use edit\extent\AbstractDelegateExtent;
use edit\extent\Extent;
use edit\functions\mask\Mask;
use edit\functions\mask\Masks;
use edit\functions\pattern\Pattern;
use edit\regions\AbstractBufferRegion;
use edit\regions\Region;

class ForgetfulExtentBuffer extends AbstractDelegateExtent implements Pattern{

	public $buffer = [];
	public $mask;
	public $min = null;
	public $max = null;

	public function __construct(Extent $delegate, Mask $mask = null){
		parent::__construct($delegate);
		if($mask == null){
			$mask = Masks::alwaysTrue();
		}

		$this->mask = $mask;
	}

	public function setBlock(Vector $location, $block) : bool{
		if($this->min == null){
			$this->min = $location;
		}else{
			$this->min = Vector::getMinimum($this->min, $location);
		}

		if($this->max == null){
			$this->max = $location;
		}else{
			$this->max = Vector::getMaximum($this->max, $location);
		}

		$blockVector = $location->toBlockVector();
		if($this->mask->test($blockVector)){
			$this->buffer[$blockVector->toString()] = $block;
			return true;
		}else{
			return $this->getExtent()->setBlock($location, $block);
		}
	}

	public function apply(Vector $pos) : BaseBlock{
		if(isset($this->buffer[$pos->toBlockVector()->toString()])){
			return $this->buffer[$pos->toBlockVector()->toString()];
		}else{
			return new BaseBlock(Block::AIR);
		}
	}

	public function asRegion() : Region{
		return new AbstractBufferRegion($this);
	}
}