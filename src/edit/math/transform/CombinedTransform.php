<?php

namespace edit\math\transform;

use edit\Vector;

class CombinedTransform implements Transform{

	private $transforms;

	public function __construct(array $transforms){
		$this->transforms = $transforms;
	}

	public function isIdentity() : bool{
		foreach($this->transforms as $transform){
			if(!$transform->isIdentity()){
				return false;
			}
		}

		return true;
	}

	public function apply(Vector vector) : Vector{
		foreach($this->transforms as $transform){
			$vector = $transform->apply($vector);
		}
		return $vector;
	}

	public function inverse() : Transform{
		$list = [];
		for($i = count($transforms - 1);$i >= 0;$i--){
			$list[] = $this->transforms[$i]->inverse();
		}
		return new CombinedTransform($list);
	}

	public function combine(Transform other) : Transform{
		if($other instanceof CombinedTransform){
			$newTransforms = [];
			foreach($this->transforms as $transform){
				$newTransforms[] = $transform;
			}
			foreach($other->transforms as $transform){
				$newTransforms[] = $transform;
			}
			return new CombinedTransform($newTransforms);
		}else{
			$newTransforms = [];
			foreach($this->transforms as $transform){
				$newTransforms[] = $transform;
			}
			$newTransforms[] = $other;
			return new CombinedTransform($newTransforms);
		}
	}

}