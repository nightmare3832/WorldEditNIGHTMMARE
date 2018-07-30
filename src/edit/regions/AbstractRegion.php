<?php

namespace edit\regions;

use pocketmine\level\Level;

use edit\Vector;

abstract class AbstractRegion implements Region{

	protected $world;

	public function __construct(Level $world){
		$this->world = $world;
	}

	public function getCenter() : Vector{
		return $this->getMinimumPoint()->add($this->getMaximumPoint())->divide(2);
	}

	public function iterator(){
	}

	public function getWorld() : Level{
		return $this->world;
	}

	public function setWorld(Level $world){
		$this->world = $world;
	}

	public function shift(Vector $change){
		$this->expand($change);
		$this->constract($change);
	}

	public function polygonize(int $maxPoints) : array{
		
	}

	public function getArea() : int{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return (int)(($max->getX() - $min->getX() + 1) *
				($max->getY() - $min->getY() + 1) *
				($max->getZ() - $min->getZ() + 1));
	}

	public function getWidth() : int{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return (int) ($max->getX() - $min->getX() + 1);
	}

	public function getHeight() : int{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return (int) ($max->getY() - $min->getY() + 1);
	}

	public function getLength() : int{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return (int) ($max->getZ() - $min->getZ() + 1);
	}
}