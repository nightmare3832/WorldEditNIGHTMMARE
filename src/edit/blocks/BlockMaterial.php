<?php

namespace edit\blocks;

interface BlockMaterial{

	function isRenderedAsNormalBlock() : bool;

	function isFullCube() : bool;

	function isOpaque() : bool;

	function isPowerSource() : bool;

	function isLiquid() : bool;

	function isSolid() : bool;

	function getHardness() : float;

	function getResistance() : float;

	function getSlipperiness() : float;

	function isGrassBlocking() : bool;

	function getAmbientOcclusionLightValue() : float;

	function getLightOpacity() : int;

	function getLightValue() : int;

	function isFragileWhenPushed() : bool;

	function isUnpushable() : bool;

	function isAdventureModeExempt() : bool;

	function isTicksRandomly() : bool;

	function isUsingNeighborLight() : bool;

	function isMovementBlocker() : bool;

	function isBurnable() : bool;

	function isToolRequired() : bool;

	function isReplacedDuringPlacement() : bool;

}
