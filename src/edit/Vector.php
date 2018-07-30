<?php

namespace edit;

use pocketmine\math\Vector3;

class Vector{

	public $x;
	public $y;
	public $z;

	public function __construct($x, $y = 0, $z = 0){
		if($x instanceof Vector){
			$this->x = $x->x;
			$this->y = $x->y;
			$this->z = $x->z;
		}else{
			$this->x = $x;
			$this->y = $y;
			$this->z = $z;
		}
	}

	public function Vector(){
		$this->x = 0;
		$this->y = 0;
		$this->z = 0;
	}

	public function getX(){
		return $this->x;
	}

	public function getBlockX(){
		return (int) round($this->x);
	}

	public function setX($x){
		return new Vector($x, $this->y, $this->z);
	}

	public function getY(){
		return $this->y;
	}

	public function getBlockY(){
		return (int) round($this->y);
	}

	public function setY($y){
		return new Vector($this->x, $y, $this->z);
	}

	public function getZ(){
		return $this->z;
	}

	public function getBlockZ(){
		return (int) round($this->z);
	}

	public function setZ($z){
		return new Vector($this->x, $this->y, $z);
	}

	public function add($x, $y = 0, $z = 0){
		if($x instanceof Vector){
			return new Vector($this->x + $x->x, $this->y + $x->y, $this->z + $x->z);
		}else if(is_numeric($x)){
			return new Vector($this->x + $x, $this->y + $y, $this->z + $z);
		}else{
			$newX = $this->x;
			$newY = $this->y;
			$newZ = $this->z;

			foreach($x as $v){
				$newX += $v->x;
				$newY += $v->y;
				$newZ += $v->z;
			}

			return new Vector($newX, $newY, $newZ);
		}
	}

	public function subtract($x, $y = 0, $z = 0){
		if($x instanceof Vector){
			return new Vector($this->x - $x->x, $this->y - $x->y, $this->z - $x->z);
		}else if(is_numeric($x)){
			return new Vector($this->x - $x, $this->y - $y, $this->z - $z);
		}else{
			$newX = $this->x;
			$newY = $this->y;
			$newZ = $this->z;

			foreach($x as $v){
				$newX -= $v->x;
				$newY -= $v->y;
				$newZ -= $v->z;
			}

			return new Vector($newX, $newY, $newZ);
		}
	}

	public function multiply($x, $y = null, $z = null){
		if($x instanceof Vector){
			return new Vector($this->x * $x->x, $this->y * $x->y, $this->z * $x->z);
		}else if(is_numeric($x)){
			if($y === null && $z === null) return new Vector($this->x * $x, $this->y * $x, $this->z * $x);
			return new Vector($this->x * $x, $this->y * $y, $this->z * $z);
		}else{
			$newX = $this->x;
			$newY = $this->y;
			$newZ = $this->z;

			foreach($x as $v){
				$newX *= $v->x;
				$newY *= $v->y;
				$newZ *= $v->z;
			}

			return new Vector($newX, $newY, $newZ);
		}
	}

	public function divide($x, $y = null, $z = null){
		if($x instanceof Vector){
			return new Vector($this->x / $x->x, $this->y / $x->y, $this->z / $x->z);
		}else if(is_numeric($x)){
			if($y === null && $z === null) return new Vector($this->x / $x, $this->y / $x, $this->z / $x);
			return new Vector($this->x / $x, $this->y / $y, $this->z / $z);
		}
	}

	public function length(){
		return sqrt($this->x * $this->x + $this->y * $this->y + $this->z * $this->z);
	}

	public function lengthSq(){
		return $this->x * $this->x + $this->y * $this->y + $this->z * $this->z;
	}

	public function distance($other){
		return sqrt(pow($other->x - $this->x, 2) +
			pow($other->y - $this->y, 2) +
			pow($other->z - $this->z, 2));
	}

	public function distanceSq($other){
		return pow($other->x - $this->x, 2) +
			pow($other->y - $this->y, 2) +
			pow($other->z - $this->z, 2);
	}

	public function normalize(){
		return $this->divide($this->length());
	}

	public function dot($other){
		return $this->x * $other->x + $this->y * $other->y + $this->z * $other->z;
	}

	public function cross($other){
		return new Vector(
			$this->y * $other->z - $this->z * $other->y,
			$this->z * $other->x - $this->x * $other->z,
			$this->x * $other->y - $this->y * $other->x
		);
	}

	public function containedWithin($min, $max){
		return $this->x >= $min->x && $this->x <= $max->x && $this->y >= $min->y && $this->y <= $max->y && $this->z >= $min->z && $this->z <= $max->z;
	}

	public function containedWithinBlock($min, $max){
		return $this->getBlockX() >= $min->getBlockX() && $this->getBlockX() <= $max->getBlockX()
			&& $this->getBlockY() >= $min->getBlockY() && $this->getBlockY() <= $max->getBlockY()
			&& $this->getBlockZ() >= $min->getBlockZ() && $this->getBlockZ() <= $max->getBlockZ();
	}

	public function clampY($min, $max){
		return new Vector($this->x, max($min, min($max, $this->y)), $this->z);
	}

	public function floor(){
		return new Vector(floor($this->x), floor($this->y), floor($this->z));
	}

	public function ceil(){
		return new Vector(ceil($this->x), ceil($this->y), ceil($this->z));
	}

	public function round(){
		return new Vector(round($this->x + 0.5), round($this->y + 0.5), round($this->z + 0.5));
	}

	public function positive(){
		return new Vector(abs($this->x), abs($this->y), abs($this->z));
	}

	public function transform2D($angle, $aboutX, $aboutZ, $translateX, $translateZ){
		$angle = deg2rad($angle);
		$x = $this->x - $aboutX;
		$z = $this->z - $aboutZ;
		$x2 = $x * cos($angle) - $z * sin($angle);
		$z2 = $x * sin($angle) + $z * cos($angle);

		return new Vector(
			$x2 + $aboutX + $translateX,
			$this->y,
			$z2 + $aboutZ + $translateZ
		);
	}

	public function isCollinearWith($other){
		if($this->x == 0 && $this->y == 0 && $this->z == 0){
			return true;
		}

		$otherX = $other->x;
		$otherY = $other->y;
		$otherZ = $other->z;

		if($otherX == 0 && $otherY == 0 && $otherZ == 0){
			return true;
		}

		if(($x == 0) != ($otherX == 0)) return false;
		if(($y == 0) != ($otherY == 0)) return false;
		if(($z == 0) != ($otherZ == 0)) return false;

		$quotientX = $otherX / $x;
		if(!is_nan($quotientX)){
			return $other->equals(multiply($quotientX));
		}

		$quotientY = $otherY / $y;
		if(!is_nan($quotientY)){
			return $other->equals(multiply($quotientY));
		}

		$quotientZ = $otherZ / $z;
		if(!is_nan($quotientZ)){
			return $other->equals(multiply($quotientZ));
		}
	}

	public function toPitch(){
		$x = $this->getX();
		$z = $this->getZ();

		if($x == 0 && $z == 0){
			return $this->getY() > 0 ? -90 : 90;
		}else{
			$x2 = $x * $x;
			$z2 = $z * $z;
			$xz = sqrt($x2 + $z2);
			return rad2deg(atan(-$this->getY() / $xz));
		}
	}

	public function toYaw(){
		$x = $this->getX();
		$z = $this->getZ();

		$t = atan2(-$x, $z);
		$_2pi = 2 * M_PI;

		return rad2deg((($t + $_2pi) % $_2pi));
	}

	public static function toBlockPoint2($x, $y, $z){
		return new BlockVector(
			floor($x),
			floor($y),
			floor($z)
		);
	}

	public function toBlockPoint(){
		return new BlockVector(
			floor($this->x),
			floor($this->y),
			floor($this->z)
		);
	}

	public function toBlockVector(){
		return new BlockVector($this);
	}

	public function toVector2D(){
		return new Vector2D($this->x, $this->z);
	}

	public function toVector3(){
		return new Vector3($this->x, $this->y, $this->z);
	}

	public function equals($obj){
		if(!($obj instanceof Vector)){
			return false;
		}

		return $obj->x == $this->x && $obj->y == $this->y && $obj->z == $this->z;
	}

	public static function getMinimum($v1, $v2){
		return new Vector(
			min($v1->x, $v2->x),
			min($v1->y, $v2->y),
			min($v1->z, $v2->z)
		);
	}

	public static function getMaximum($v1, $v2){
		return new Vector(
			max($v1->x, $v2->x),
			max($v1->y, $v2->y),
			max($v1->z, $v2->z)
		);
	}

	public static function getMidpoint($v1, $v2){
		return new Vector(
			($v1->x + $v2->x) / 2,
			($v1->y + $v2->y) / 2,
			($v1->z + $v2->z) / 2
		);
	}

	public function toString() : string{
		return "(" . $this->getX() . ", " . $this->getY() . ", " . $this->getZ() . ")";
	}
}