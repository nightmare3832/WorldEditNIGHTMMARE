<?php

namespace edit;

class BlockVector2D extends Vector2D{

	public function equals($obj){
		if(!($obj instanceof Vector2D)){
			return false;
		}

		return (int) $obj->x == (int) $this->x && (int) $obj->y == (int) $this->y;
	}

	public function toBlockVector2D(){
		return $this;
	}
}