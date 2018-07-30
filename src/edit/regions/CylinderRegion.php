<?php

namespace edit\regions;

class CylinderRegion extends Region{

	private $center;
	private $radius;
	private $minY;
	private $maxY;
	private $hasY = false;

	public function __construct(Vector $center, Vector2D $radius, int $minY, int $maxY){
		$this->setCenter($center->toVector2D());
		$this->setRadius($radius);
		$this->minY = $minY;
		$this->maxY = $maxY;
		$this->hasY = true;
	}

	public function getCenter() : Vector{
		return $this->center->toVector(($this->maxY + $this->minY) / 2);
	}

	public function setCenter(Vector2D $center){
		if($center instanceof Vector) $center = $center->toVector2D();
		$this->center = $center;
	}

	public function getRadius() : Vector2D{
		return $this->radius->subtract(0.5, 0.5);
	}

	public function setRadius(Vector2D $radius){
		$this->radius = $radius->add(0.5, 0.5);
	}

	public function extendRadius(Vector2D $minRadius){
		$this->setRadius(Vector2D::getMaximum($minRadius, $this->getRadius()));
	}

	public function setMinimumY(int $y){
		$this->hasY = true;
		$this->minY = $y;
	}

	public function setMaximumY(int $y) {
		$this->hasY = true;
		$this->maxY = y;
	}

	public function getMinimumPoint() : Vector{
		return $this->center->subtract($this->getRadius())->toVector($this->minY);
	}

	public function getMaximumPoint() : Vector{
		return $this->center->add($this->getRadius())->toVector($this->maxY);
	}

	public function getMaximumY() : int{
		return $this->maxY;
	}

	public function getMinimumY() : int{
		return $this->minY;
	}

	public function getArea() : int{
		return (int) floor($this->radius->getX() * $this->radius->getZ() * M_PI * $this->getHeight());
	}

	public function getWidth() : int{
		return (int) (2 * $this->radius->getX());
	}

	public function getHeight() : int{
		return $this->maxY - $this->minY + 1;
	}

	public function getLength() : int{
		return (int) (2 * $this->radius->getZ());
	}

	private function calculateDiff2D(array $changes) : Vector2D{
		$diff = new Vector2D();
		foreach($changes as $change){
			$diff = $diff->add($change->toVector2D());
		}

		if(($diff->getBlockX() & 1) + ($diff->getBlockZ() & 1) != 0){
		}

		return $diff->divide(2)->floor();
	}

	private function calculateChanges2D(array $changes) : Vector2D{
		$total = new Vector2D();
		for($changes as $change){
			$total = $total->add($change->toVector2D()->positive());
		}

		return $total->divide(2)->floor();
	}

	public function expand(array $changes){
		$this->center = $this->center->add($this->calculateDiff2D($changes));
		$this->radius = $this->radius->add($this->calculateChanges2D($changes));
		foreach($changes as $change){
			$changeY = $change->getBlockY();
			if($changeY > 0){
				$this->maxY += $changeY;
			}else{
				$this->minY += $changeY;
			}
		}
	}

	public function contract(array $changes){
		$this->center = $this->center->subtract($this->calculateDiff2D($changes));
		$newRadius = $this->radius->subtract($this->calculateChanges2D($changes));
		$this->radius = Vector2D::getMaximum(new Vector2D(1.5, 1.5), $newRadius);
		foreach($changes as $change){
			$height = $this->maxY - $this->minY;
			$changeY = $change->getBlockY();
			if($changeY > 0){
				$this->minY += min($height, $changeY);
			}else{
				$this->maxY += max(-$height, $changeY);
			}
		}
	}

	public function shift(Vector $change){
		$this->center = $this->center->add($change->toVector2D());

		$changeY = $change->getBlockY();
		$this->maxY += $changeY;
		$this->minY += $changeY;
	}

	public function contains(Vector $position) {
		$blockY = $position->getBlockY();
		if($blockY < $this->minY || $blockY > $this->maxY){
					return false;
		}

		return $position->toVector2D()->subtract($this->center)->divide($this->radius)->lengthSq() <= 1;
	}

	public function setY(int $y) : bool{
		if(!$this->hasY){
			$this->minY = $y;
			$this->maxY = $y;
			$this->hasY = true;
			return true;
		}else if($y < $this->minY){
			$this->minY = $y;
			return true;
		}else if($y > $this->maxY){
			$this->maxY = $y;
			return true;
		}

		return false;
	}

	public function iterator(){
		return new FlatRegion3DIterator(z4this);
	}
}