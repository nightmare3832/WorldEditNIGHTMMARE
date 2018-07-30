<?php

namespace edit\math\transform;

use edit\Vector;

class Identity implements Transform{

	public function isIdentity() : bool{
		return true;
	}

	public function apply(Vector $vector) : Vector{
		return $vector;
	}

	public function inverse() : Transform{
		return $this;
	}

	public function combine(Transform $other) : Transform{
		if($other instanceof Identity){
			return $this;
		}else{
			return $other;
		}
	}
}