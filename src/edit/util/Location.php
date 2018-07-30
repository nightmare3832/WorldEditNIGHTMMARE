<?php

namespace edit\util;

use edit\Vector;
use edit\extent\Extent;

class Location{

	private $extent;
	private $position;
	private $pitch;
	private $yaw;

	public function __construct(Extent $extent, Vector $position, float $yaw, float $pitch){
		$this->extent = $extent;
		$this->position = $position;
		$this->yaw = $yaw;
		$this->pitch = $pitch;
	}

	public function getExtent() : Extent{
		return $this->extent;
	}

	public function setExtent(Extent $extent) : Location{
		return new Location($extent, $this->position, $this->yaw, $this->pitch);
	}

	public function getYaw() : float{
		return $this->yaw;
	}

	public function setYaw(float $yaw) : Location{
		return new Location($this->extent, $this->position, $yaw, $this->pitch);
	}

	public function getPitch() : float{
		return $this->pitch;
	}

	public function setPitch(float $pitch) : Location{
		return new Location($this->extent, $this->position, $this->yaw, $pitch);
	}

	public function setDirection(float $yaw, float $pitch) : Location{
		return new Location($this->extent, $this->position, $yaw, $pitch);
	}

	public function getDirection() : Vector{
		$yaw = deg2rad($this->getYaw());
		$pitch = deg2rad($this->getPitch());
		$xz = cos($pitch);
		return new Vector(
			-$xz * sin($yaw),
			-sin($pitch),
			$xz * cos($yaw));
	}

	public function toVector() : Vector{
		return $this->position;
	}

	public function getX() : float{
		return $this->position->getX();
	}

	public function getBlockX() : float{
		return $this->position->getBlockX();
	}

	public function setX(float $x) : float{
		return new Location($this->extent, $this->position->setX($x), $this->yaw, $this->pitch);
	}

	public function getY() : float{
		return $this->position->getY();
	}

	public function getBlockY() : float{
		return $this->position->getBlockY();
	}

	public function setY(float $y) : float{
		return new Location($this->extent, $this->position->setY($y), $this->yaw, $this->pitch);
	}

	public function getZ() : float{
		return $this->position->getZ();
	}

	public function getBlockZ() : float{
		return $this->position->getBlockZ();
	}

	public function setZ(float $z) : float{
		return new Location($this->extent, $this->position->setZ($z), $this->yaw, $this->pitch);
	}

//TODO more functions
}