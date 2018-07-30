<?php

namespace edit;

use pocketmine\level\Level;

class WorldVector extends Vector{

	private $world;

	public function __construct(Level $world, $x, $y = 0, $z = 0){
		parent::__construct($x, $y, $z);
		$this->world = $world;
	}

	public function getWorld() : Level{
		return $this->world;
	}

	public function toWorldBlockVector() : BlockWorldVector{
		return new BlockWorldVector($this);
	}

	//public function toLocation() : Location{
	//}
}