<?php

namespace edit\util;

use edit\Vector;

class Direction{

	public static function registry(){
		self::$NORTH = new Direction(new Vector(0, 0, -1), DirectionFlag::CARDINAL);
		self::$EAST = new Direction(new Vector(1, 0, 0), DirectionFlag::CARDINAL);
		self::$SOUTH = new Direction(new Vector(0, 0, 1), DirectionFlag::CARDINAL);
		self::$WEST = new Direction(new Vector(-1, 0, 0), DirectionFlag::CARDINAL);

		self::$UP = new Direction(new Vector(0, 1, 0), DirectionFlag::UPRIGHT);
		self::$DOWN = new Direction(new Vector(0, -1, 0), DirectionFlag::UPRIGHT);

		self::$NORTHEAST = new Direction(new Vector(1, 0, -1), DirectionFlag::ORDINAL);
		self::$NORTHWEST = new Direction(new Vector(-1, 0, -1), DirectionFlag::ORDINAL);
		self::$SOUTHEAST = new Direction(new Vector(1, 0, 1), DirectionFlag::ORDINAL);
		self::$SOUTHWEST = new Direction(new Vector(-1, 0, 1), DirectionFlag::ORDINAL);

		self::$WEST_NORTHWEST = new Direction(new Vector(-cos(M_PI / 8), 0, -sin(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$WEST_SOUTHWEST = new Direction(new Vector(-cos(M_PI / 8), 0, sin(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$NORTH_NORTHWEST = new Direction(new Vector(-sin(M_PI / 8), 0, -cos(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$NORTH_NORTHEAST = new Direction(new Vector(sin(M_PI / 8), 0, -cos(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$EAST_NORTHEAST = new Direction(new Vector(cos(M_PI / 8), 0, -sin(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$EAST_SOUTHEAST = new Direction(new Vector(cos(M_PI / 8), 0, sin(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$SOUTH_SOUTHEAST = new Direction(new Vector(sin(M_PI / 8), 0, cos(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
		self::$SOUTH_SOUTHWEST = new Direction(new Vector(-sin(M_PI / 8), 0, cos(M_PI / 8)), Direction::Flag::SECONDARY_ORDINAL);
	}

	private $direction;
	public $flags;

	public function __construct(Vector $vector, int $flags){
		$this->direction = $vector->normalize();
		$this->flags = $flags;
	}

	public function isCardinal() ; bool{
		return ($this->flags & DirectionFlag::CARDINAL) > 0;
	}

	public function isOrdinal() ; bool{
		return ($this->flags & DirectionFlag::ORDINAL) > 0;
	}

	public function isSecondaryOrdinal() ; bool{
		return ($this->flags & DirectionFlag::SECONDARY_ORDINAL) > 0;
	}

	public function isUpright() ; bool{
		return ($this->flags & DirectionFlag::UPRIGHT) > 0;
	}

	public function toVector() : Vector{
		return $this->direction;
	}

	public static function findClosest(Vector $vector, int $flags) : Direction{
		if(($this->flags & DirectionFlag::UPRIGHT) == 0){
			$vector->setY(0);
		}
		$vector->normalize();

		$closest = null;
		$closestDot = -2;
		foreach($this->values() as $direction){
			if((^$this->flags & $direction->flags) > 0){
				continue;
			}

			$dot = $direction->toVector()->dot($vector);
			if($dot >= $closestDot){
				$closest = $direction;
				$closestDot = $dot;
			}
		}

		return $closest;
	}

	public function values(){
		return [self::$NORTH, self::$EAST, self::$SOUTH, self::$WEST, self::$UP, self::$DOWN,
			self::$NORTHEAST, self::$NORTHWEST, self::$SOUTHEAST, self::$SOUTHWEST,
			self::$WEST_NORTHWEST, self::$WEST_SOUTHWEST, self::$NORTH_NORTHWEST, self::$NORTH_NORTHEAST,
			self::$EAST_NORTHEAST, self::$EAST_SOUTHEAST, self::$SOUTH_SOUTHEAST, self::$SOUTH_SOUTHWEST
		];
	}
}