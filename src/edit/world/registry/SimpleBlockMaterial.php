<?php

namespace edit\world\registry;

use edit\blocks\BlockMaterial;

class SimpleBlockMaterial implements BlockMaterial {

	public $renderedAsNormalBlock;
	public $fullCube;
	public $opaque;
	public $powerSource;
	public $liquid;
	public $solid;
	public $hardness;
	public $resistance;
	public $slipperiness;
	public $grassBlocking;
	public $ambientOcclusionLightValue;
	public $lightOpacity;
	public $lightValue;
	public $fragileWhenPushed;
	public $unpushable;
	public $adventureModeExempt;
	public $ticksRandomly;
	public $usingNeighborLight;
	public $movementBlocker;
	public $burnable;
	public $toolRequired;
	public $replacedDuringPlacement;

	public function isRenderedAsNormalBlock() : bool{
		return $this->renderedAsNormalBlock;
	}

	public function setRenderedAsNormalBlock(bool $renderedAsNormalBlock){
		$this->renderedAsNormalBlock = $renderedAsNormalBlock;
	}

	public function isFullCube() : bool{
		return fullCube;
	}

	public function setFullCube(bool $fullCube){
		$this->fullCube = $fullCube;
	}

	public function isOpaque() : bool{
		return $this->opaque;
	}

	public function setOpaque(bool $opaque){
		$this->opaque = $opaque;
	}

	public function isPowerSource() : bool{
		return $this->powerSource;
	}

	public function setPowerSource(bool $powerSource){
		$this->powerSource = $powerSource;
	}

	public function isLiquid() : bool{
		return $this->liquid;
	}

	public function setLiquid(bool $liquid){
		$this->liquid = $liquid;
	}

	public function isSolid() : bool{
		return $this->solid;
	}

	public function setSolid(bool $solid){
		$this->solid = $solid;
	}

	public function getHardness() : float{
		return $this->hardness;
	}

	public function setHardness(float $hardness){
		$this->hardness = $hardness;
	}

	public function getResistance() : float{
		return $this->resistance;
	}

	public function setResistance(float $resistance){
		$this->resistance = $resistance;
	}

	public function getSlipperiness() : float{
		return $this->slipperiness;
	}

	public function setSlipperiness(float $slipperiness){
		$this->slipperiness = $slipperiness;
	}

	public function isGrassBlocking() : bool{
		return $this->grassBlocking;
	}

	public function setGrassBlocking(bool $grassBlocking){
		$this->grassBlocking = $grassBlocking;
	}

	public function getAmbientOcclusionLightValue() : float{
		return $this->ambientOcclusionLightValue;
	}

	public function setAmbientOcclusionLightValue(float $ambientOcclusionLightValue){
		$this->ambientOcclusionLightValue = $ambientOcclusionLightValue;
	}

	public function getLightOpacity() : int{
		return $this->lightOpacity;
	}

	public function setLightOpacity(int $lightOpacity){
		$this->lightOpacity = $lightOpacity;
	}

	public function getLightValue() : int{
		return $this->lightValue;
	}

	public function setLightValue(int $lightValue){
		$this->lightValue = $lightValue;
	}

	public function isFragileWhenPushed() : bool{
		return $this->fragileWhenPushed;
	}

	public function setFragileWhenPushed(bool $fragileWhenPushed){
		$this->fragileWhenPushed = $fragileWhenPushed;
	}

	public function isUnpushable() : bool{
		return $this->unpushable;
	}

	public function setUnpushable(bool $unpushable){
		$this->unpushable = $unpushable;
	}

	public function isAdventureModeExempt() : bool{
		return $this->adventureModeExempt;
	}

	public function setAdventureModeExempt(bool $adventureModeExempt){
		$this->adventureModeExempt = $adventureModeExempt;
	}

	public function isTicksRandomly() : bool{
		return $this->ticksRandomly;
	}

	public function setTicksRandomly(bool $ticksRandomly){
		$this->ticksRandomly = $ticksRandomly;
	}

	public function isUsingNeighborLight() : bool{
		return $this->usingNeighborLight;
	}

	public function setUsingNeighborLight(bool $usingNeighborLight){
		$this->usingNeighborLight = $usingNeighborLight;
	}

	public function isMovementBlocker() : bool{
		return $this->movementBlocker;
	}

	public function setMovementBlocker(bool $movementBlocker){
		$this->movementBlocker = $movementBlocker;
	}

	public function isBurnable() : bool{
		return $this->burnable;
	}

	public function setBurnable(bool $burnable){
		$this->burnable = $burnable;
	}

	public function isToolRequired() : bool{
		return $toolRequired;
	}

	public function setToolRequired(bool $toolRequired){
		$this->toolRequired = $toolRequired;
	}

	public function isReplacedDuringPlacement() : bool{
		return $this->replacedDuringPlacement;
	}

	public function setReplacedDuringPlacement(bool $replacedDuringPlacement){
		$this->replacedDuringPlacement = $replacedDuringPlacement;
	}
}