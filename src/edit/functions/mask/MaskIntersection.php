<?php

namespace edit\functions\mask;

class MaskIntersection extends AbstractMask{

	private $mask = [];

	public function __construct(array $masks){
		$this->masks = $masks;
	}

	public function add(array $masks){
		foreach($masks as $mask){
			$this->masks[] = $mask;
		}
	}

	public function getMasks() : array{
		return $this->masks;
	}

	public function test($vector) : bool{
		if(count($this->masks) == 0){
			return false;
		}

		foreach($this->masks as $mask){
			if(!$mask->test($vector)){
				return false;
			}
		}

		return true;
	}

	public function toMask2D() : ?Mask2D{
		$mask2dList = [];
		foreach($this->masks as $mask){
			$mask2d = $mask->toMask2D();
			if($mask2d != null){
				$mask2dList[] = $mask2d;
			}else{
				return null;
			}
		}
		return new MaskIntersection2D($mask2dList);
	}
}