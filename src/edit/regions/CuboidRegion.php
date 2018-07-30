<?php

namespace edit\regions;

use edit\Vector;
use edit\Vector2D;
use edit\util\UtilIterable;

class CuboidRegion extends AbstractRegion implements FlatRegion{

	private $pos1;
	private $pos2;

	public function __construct($pos1, $pos2){
		$this->pos1 = $pos1;
		$this->pos2 = $pos2;
	}

	public function getPos1() : Vector{
		return $this->pos1;
	}

	public function setPos1(?Vector $pos1){
		$this->pos1 = $pos1;
	}

	public function getPos2() : Vector{
		return $this->pos2;
	}

	public function setPos2(?Vector $pos2){
		$this->pos2 = $pos2;
	}

	private function recalculate(){
		$this->pos1 = $this->pos1->clampY(0, 255);
		$this->pos2 = $this->pos2->clampY(0, 255);
	}

	public function getFaces() : Region{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return new RegionIntersection([
				new CuboidRegion($this->pos1->setX($min->getX()), $this->pos2->setX($min->getX())),
				new CuboidRegion($this->pos1->setX($max->getX()), $this->pos2->setX($max->getX())),

				new CuboidRegion($this->pos1->setZ($min->getZ()), $this->pos2->setZ($min->getZ())),
				new CuboidRegion($this->pos1->setZ($max->getZ()), $this->pos2->setZ($max->getZ())),

				new CuboidRegion($this->pos1->setY($min->getY()), $this->pos2->setY($min->getY())),
				new CuboidRegion($this->pos1->setY($max->getY()), $this->pos2->setY($max->getY()))
		]);
	}

	public function getWalls() : Region{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return new RegionIntersection([
				new CuboidRegion($this->pos1->setX($min->getX()), $this->pos2->setX($min->getX())),
				new CuboidRegion($this->pos1->setX($max->getX()), $this->pos2->setX($max->getX())),

				new CuboidRegion($this->pos1->setZ($min->getZ()), $this->pos2->setZ($min->getZ())),
				new CuboidRegion($this->pos1->setZ($max->getZ()), $this->pos2->setZ($max->getZ()))
		]);
	}

	public function getMinimumPoint() : Vector{
		return new Vector(min($this->pos1->getX(), $this->pos2->getX()),
				min($this->pos1->getY(), $this->pos2->getY()),
				min($this->pos1->getZ(), $this->pos2->getZ()));
	}

	public function getMaximumPoint() : Vector{
		return new Vector(max($this->pos1->getX(), $this->pos2->getX()),
				max($this->pos1->getY(), $this->pos2->getY()),
				max($this->pos1->getZ(), $this->pos2->getZ()));
	}

	public function getMinimumY() : int{
		return min($this->pos1->getBlockY(), $this->pos2->getBlockY());
	}

	public function getMaximumY() : int{
		return max($this->pos1->getBlockY(), $this->pos2->getBlockY());
	}

	public function expand(array $changes){
		foreach($changes as $change) {
			if($change->getX() > 0){
				if(max($this->pos1->getX(), $this->pos2->getX()) == $this->pos1->getX()){
					$this->pos1 = $this->pos1->add(new Vector($change->getX(), 0, 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector($change->getX(), 0, 0));
				}
			}else{
				if(min($this->pos1->getX(), $this->pos2->getX()) == $this->pos1->getX()){
					$this->pos1 = $this->pos1->add(new Vector($change->getX(), 0, 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector($change->getX(), 0, 0));
				}
			}

			if($change->getY() > 0){
				if(max($this->pos1->getY(), $this->pos2->getY()) == $this->pos1->getY()){
					$this->pos1 = $this->pos1->add(new Vector(0, $change->getY(), 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, $change->getY(), 0));
				}
			}else{
				if(min($this->pos1->getY(), $this->pos2->getY()) == $this->pos1->getY()){
					$this->pos1 = $this->pos1->add(new Vector(0, $change->getY(), 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, $change->getY(), 0));
				}
			}

			if($change->getZ() > 0){
				if(max($this->pos1->getZ(), $this->pos2->getZ()) == $this->pos1->getZ()){
					$this->pos1 = $this->pos1->add(new Vector(0, 0, $change->getZ()));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, 0, $change->getZ()));
				}
			}else{
				if(min($this->pos1->getZ(), $this->pos2->getZ()) == $this->pos1->getZ()){
					$this->pos1 = $this->pos1->add(new Vector(0, 0, $change->getZ()));
				} else {
					$this->pos2 = $this->pos2->add(new Vector(0, 0, $change->getZ()));
				}
			}
		}

		$this->recalculate();
	}

	public function contract(array $changes){
		foreach($changes as $change){
			if ($change->getX() < 0) {
				if (max($this->pos1->getX(), $this->pos2->getX()) == $this->pos1->getX()) {
					$this->pos1 = $this->pos1->add(new Vector($change->getX(), 0, 0));
				} else {
					$this->pos2 = $this->pos2->add(new Vector($change->getX(), 0, 0));
				}
			}else{
				if(min($this->pos1->getX(), $this->pos2->getX()) == $this->pos1->getX()) {
					$this->pos1 = $this->pos1->add(new Vector($change->getX(), 0, 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector($change->getX(), 0, 0));
				}
			}

			if($change->getY() < 0){
				if(max($this->pos1->getY(), $this->pos2->getY()) == $this->pos1->getY()){
					$this->pos1 = $this->pos1->add(new Vector(0, $change->getY(), 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, $change->getY(), 0));
				}
			}else{
				if(min($this->pos1->getY(), $this->pos2->getY()) == $this->pos1->getY()){
					$this->pos1 = $this->pos1->add(new Vector(0, $change->getY(), 0));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, $change->getY(), 0));
				}
			}

			if($change->getZ() < 0){
				if(max($this->pos1->getZ(), $this->pos2->getZ()) == $this->pos1->getZ()){
					$this->pos1 = $this->pos1->add(new Vector(0, 0, $change->getZ()));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, 0, $change->getZ()));
				}
			}else{
				if(min($this->pos1->getZ(), $this->pos2->getZ()) == $this->pos1->getZ()){
					$this->pos1 = $this->pos1->add(new Vector(0, 0, $change->getZ()));
				}else{
					$this->pos2 = $this->pos2->add(new Vector(0, 0, $change->getZ()));
				}
			}
		}

		$this->recalculate();
	}

	public function shift(Vector $change){
		$this->pos1 = $this->pos1->add($change);
		$this->pos2 = $this->pos2->add($change);

		$this->recalculate();
	}

	public function contains(Vector $position) : bool{
		$x = $position->getX();
		$y = $position->getY();
		$z = $position->getZ();

		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		return $x >= $min->getBlockX() && $x <= $max->getBlockX()
				&& $y >= $min->getBlockY() && $y <= $max->getBlockY()
				&& $z >= $min->getBlockZ() && $z <= $max->getBlockZ();
	}

	public function iterator() : array{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		$result = [];

		for($x = $min->getX();$x <= $max->getX();$x++){
			for($y = $min->getY();$y <= $max->getY();$y++){
				for($z = $min->getZ();$z <= $max->getZ();$z++){
					$result[] = new Vector($x, $y, $z);
				}
			}
		}

		return $result;
	}

	public function asFlatRegion() : array{
		$min = $this->getMinimumPoint();
		$max = $this->getMaximumPoint();

		$result = [];

		for($x = $min->getX();$x <= $max->getX();$x++){
			for($z = $min->getZ();$z <= $max->getZ();$z++){
				$result[] = new Vector2D($x, $z);
			}
		}

		return $result;
	}

}