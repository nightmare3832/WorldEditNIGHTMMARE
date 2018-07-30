<?php

namespace edit\regions;

use pocketmine\level\Level;
use pocketmine\Player;

use edit\Vector;
use edit\BlockVector;

interface RegionSelector{

	public function getWorld() : Level;

	public function setWorld(Level $world);

	public function selectPrimary(Vector $position) : bool;

	public function selectSecondary(Vector $position) : bool;

	public function explainPrimarySelection(Player $player);

	public function explainSecondarySelection(Player $player);

	public function explainRegionAdjust(Player $player);

	public function getPrimaryPosition() : BlockVector;

	public function getRegion() : Region;

	public function getIncompleteRegion() : Region;

	public function isDefined() : bool;

	public function getArea() : int;

	public function learnChanges();

	public function clear();

	public function getTypeName() : string;

	public function getInformationLines() : array;

}