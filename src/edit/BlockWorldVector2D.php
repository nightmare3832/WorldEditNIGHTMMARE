<?php

namespace edit;

class BlockWorldVector2D extends WorldVector2D{

	public function equals($obj){
		if(!($obj instanceof WorldVector2D)){
			return false;
		}

		return (int) $obj->x == (int) $this->x && (int) $obj->y == (int) $this->y;
	}
}