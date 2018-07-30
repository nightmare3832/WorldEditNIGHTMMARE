<?php

namespace edit\world\registry;

use edit\blocks\BaseBlock;
use edit\Vector;

class SimpleStateValue implements StateValue{

	public $state;
	public $data;
	public $direction;

	public function setState(SimpleState $state){
		$this->state = $state;
	}

	public function isSet(BaseBlock $block) : bool{
		return /*$this->data != null && */($block->getData() & $this->state->getDataMask()) == $this->data;
	}

	public function set(BaseBlock $block) : bool{
		//if($this->data != null){
			$block->setData(($block->getData() & ~$this->state->getDataMask()) | $this->data);
			return true;
		//}else{
		//	return false;
		//}
	}

	public function getDirection() : ?Vector{
		return $this->direction;
	}
}