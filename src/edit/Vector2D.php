<?php

namespace edit;

class Vector2D{

	public $x;
	public $z;

	public function __construct($x, $z = 0){
		if($x instanceof Vector2D){
			$this->x = $x->x;
			$this->z = $x->z;
		}else{
			$this->x = $x;
			$this->z = $z;
		}
	}

	public function Vector2D(){
		$this->x = 0;
		$this->z = 0;
	}

	public function getX(){
		return $this->x;
	}

	public function getBlockX(){
		return (int) round($this->x);
	}

	public function setX($x){
		return new Vector2D($x, $this->z);
	}

	public function getZ(){
		return $this->z;
	}

	public function getBlockZ(){
		return (int) round($this->z);
	}

	public function setZ($z){
		return new Vector2D($this->x, $z);
	}

	public function add($x, $z = 0){
		if($x instanceof Vector2D){
			return new Vector2D($this->x + $x->x, $this->z + $x->z);
		}else if(is_numeric($x)){
			return new Vector2D($this->x + $x, $this->z + $z);
		}else{
			$newX = $this->x;
			$newZ = $this->z;

			foreach($x as $v){
				$newX += $v->x;
				$newZ += $v->z;
			}

			return new Vector2D($newX, $newZ);
		}
	}

	public function subtract($x, $z = 0){
		if($x instanceof Vector2D){
			return new Vector2D($this->x - $x->x, $this->z - $x->z);
		}else if(is_numeric($x)){
			return new Vector2D($this->x - $x, $this->z - $z);
		}else{
			$newX = $this->x;
			$newZ = $this->z;

			foreach($x as $v){
				$newX -= $v->x;
				$newZ -= $v->z;
			}

			return new Vector2D($newX, $newZ);
		}
	}

	public function multiply($x, $z = -999){
		if($x instanceof Vector2D){
			return new Vector2D($this->x * $x->x, $this->z * $x->z);
		}else if(is_numeric($x)){
			if($z == 999) return new Vector2D($this->x * $x, $this->z * $x);
			return new Vector2D($this->x * $x, $this->z * $z);
		}else{
			$newX = $this->x;
			$newZ = $this->z;

			foreach($x as $v){
				$newX *= $v->x;
				$newZ *= $v->z;
			}

			return new Vector2D($newX, $newZ);
		}
	}

	public function divide($x, $z = -999){
		if($x instanceof Vector2D){
			return new Vector2D($this->x / $x->x, $this->y / $x->y, $this->z / $x->z);
		}else if(is_numeric($x)){
			if($z == 999) return new Vector2D($this->x / $x, $this->z / $x);
			return new Vector2D($this->x / $x, $this->z / $z);
		}
	}

	public function length(){
		return sqrt($this->x * $this->x + $this->z * $this->z);
	}

	public function lengthSq(){
		return $this->x * $this->x + $this->z * $this->z;
	}

	public function distance($other){
		return sqrt(pow($other->x - $this->x, 2) +
			pow($other->z - $this->z, 2));
	}

	public function distanceSq($other){
		return pow($other->x - $this->x, 2) +
			pow($other->z - $this->z, 2);
	}

	public function normalize(){
		return $this->divide($this->length());
	}

	public function dot($other){
		return $this->x * $other->x + $this->z * $other->z;
	}

	public function containedWithin($min, $max){
		return $this->x >= $min->x && $this->x <= $max->x && $this->z >= $min->z && $this->z <= $max->z;
	}

	public function containedWithinBlock($min, $max){
		return $this->getBlockX() >= $min->getBlockX() && $this->getBlockX() <= $max->getBlockX()
			&& $this->getBlockZ() >= $min->getBlockZ() && $this->getBlockZ() <= $max->getBlockZ();
	}

	public function floor(){
		return new Vector(floor($this->x), floor($this->z));
	}

	public function ceil(){
		return new Vector(ceil($this->x), ceil($this->z));
	}

	public function round(){
		return new Vector(round($this->x + 0.5), round($this->z + 0.5));
	}

	public function positive(){
		return new Vector(abs($this->x), abs($this->z));
	}

	public function transform2D($angle, $aboutX, $aboutZ, $translateX, $translateZ){
		$angle = deg2rad($angle);
		$x = $this->x - $aboutX;
		$z = $this->z - $aboutZ;
		$x2 = $x * cos($angle) - $z * sin($angle);
		$z2 = $x * sin($angle) + $z * cos($angle);

		return new Vector2D(
			$x2 + $aboutX + $translateX,
			$z2 + $aboutZ + $translateZ
		);
	}

	public function isCollinearWith($other){
		if($this->x == 0 && $this->z == 0){
			return true;
		}

		$otherX = $other->x;
		$otherZ = $other->z;

		if($otherX == 0 && $otherZ == 0){
			return true;
		}

		if(($x == 0) != ($otherX == 0)) return false;
		if(($z == 0) != ($otherZ == 0)) return false;

		$quotientX = $otherX / $x;
		if(!is_nan($quotientX)){
			return $other->equals(multiply($quotientX));
		}

		$quotientZ = $otherZ / $z;
		if(!is_nan($quotientZ)){
			return $other->equals(multiply($quotientZ));
		}

		//error
	}

	public function toBlockVector2D(){
		return new BlockVector2D($this);
	}

	public function toVector($y = 0){
		return new Vector($this->x, $y, $this->z);
	}

	public function equals($obj){
		if(!($obj instanceof Vector2D)){
			return false;
		}

		return $obj->x == $this->x && $obj->z == $this->z;
	}

	public function hashCode(){
		$hash = 7;

		//$hash = 79 * $hash (int) ($this->x ^ $this->x >>> 32);
		//$hash = 79 * $hash (int) ($this->z ^ $this->z >>> 32);
		return $hash;
	}

	public static function getMinimum($v1, $v2){
		return new Vector2D(
			min($v1->x, $v2->x),
			min($v1->z, $v2->z)
		);
	}

	public static function getMaximum($v1, $v2){
		return new Vector2D(
			max($v1->x, $v2->x),
			max($v1->z, $v2->z)
		);
	}

	public static function getMidpoint($v1, $v2){
		return new Vector2D(
			($v1->x + $v2->x) / 2,
			($v1->z + $v2->z) / 2
		);
	}
}