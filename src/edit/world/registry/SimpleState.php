<?php

namespace edit\world\registry;

use edit\blocks\BaseBlock;

class SimpleState implements State{

	public $dataMask;
	public $values;

	public function valueMap() : array{
		return $this->values;
	}

	public function getValue(BaseBlock $block) : ?StateValue{
		foreach($this->values as $value){
			if($value->isSet($block)){
				return $value;
			}
		}

		return null;
	}

	public function getDataMask() : int{
		return $this->dataMask != null ? $this->dataMask : 0xF;
	}

	public function hasDirection() : bool{
		foreach($this->values as $value){
			if($value->getDirection() != null){
				return true;
			}
		}

		return false;
	}

	function postDeserialization(){
		foreach($this->values as $v){
			$v->setState($this);
		}
	}
}