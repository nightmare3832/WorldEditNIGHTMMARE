<?php

namespace edit\regions\selector;

use pocketmine\level\Level;
use pocketmine\Player;

use edit\regions\RegionSelector;
use edit\regions\CuboidRegion;
use edit\regions\Region;
use edit\BlockVector;
use edit\Vector;
use edit\Main;

class CuboidRegionSelector implements RegionSelector{

	public $region;

	public $position1 = null;
	public $position2 = null;

	public function __construct($world, ?Vector $position1 = null, ?Vector $position2 = null){
		if($world instanceof Level){
			$this->region = new CuboidRegion($world, $position1, $position2);
			$this->position1 = $position1;
			$this->position2 = $position2;
		}else{
			$this->region = new CuboidRegion($world->getIncompleteRegion()->getWorld(), null, null);

			if($world instanceof CuboidRegionSelector){
				$this->position1 = $world->position1;
				$this->position2 = $world->position2;
			}else{
				$oldRegion = $world->getRegion();

				$this->position1 = $oldRegion->getMinimumPoint()->toBlockVector();
				$this->position1 = $oldRegion->getMaximumPoint()->toBlockVector();
			}
		}

		$this->region->setPos1($this->position1);
		$this->region->setPos2($this->position2);
	}

	public function getWorld() : Level{
		return $this->region->getWorld();
	}

	public function setWorld(Level $world){
		$this->region->setWorld($world);
	}

	public function selectPrimary(Vector $position) : bool{
		//if($this->position1 != null && ($position->compareTo($this->position1) == 0)){
		//	return false;
		//}

		$this->position1 = $position->toBlockVector();
		$this->region->setPos1($this->position1);
		return true;
	}

	public function selectSecondary(Vector $position) : bool{
		//if($this->position2 != null && ($position->compareTo($this->position2) == 0)){
		//	return false;
		//}

		$this->position2 = $position->toBlockVector();
		$this->region->setPos2($this->position2);
		return true;
	}

	public function explainPrimarySelection(Player $player){
		if($this->position1 != null && $this->position2 != null){
			$player->sendMessage(Main::LOGO."Pos1を設定しました " . $this->position1->toString() . " (" . $this->region->getArea() . ").");
		}else{
			$player->sendMessage(Main::LOGO."Pos1を設定しました " . $this->position1->toString() . ".");
		}
	}

	public function explainSecondarySelection(Player $player){
		if($this->position1 != null && $this->position2 != null){
			$player->sendMessage(Main::LOGO."Pos2を設定しました " . $this->position2->toString() . " (" . $this->region->getArea() . ").");
		}else{
			$player->sendMessage(Main::LOGO."Pos2を設定しました " . $this->position2->toString() . ".");
		}
	}

	public function explainRegionAdjust(Player $player){
		$this->explanePrimarySelection($player);
		$this->explaneSecondarySelection($player);
	}

	public function getPrimaryPosition() : BlockVector{
		return $this->position1;
	}

	public function getSecondaryPosition() : BlockVector{
		return $this->position2;
	}

	public function isDefined() : bool{
		return $this->position1 != null && $this->position2 != null;
	}

	public function getRegion() : Region{
		return $this->region;
	}

	public function getIncompleteRegion() : Region{
		return $this->region;
	}

	public function learnChanges(){
		$this->position1 = $this->region->getPos1()->toBlockVector();
		$this->position2 = $this->region->getPos2()->toBlockVector();
	}

	public function clear(){
		$this->position1 = null;
		$this->position2 = null;
	}

	public function getTypeName() : string{
		return "cuboid";
	}

	public function getInformationLines() : array{
		$lines = [];

		if($this->position1 != null){
			$lines[] = "Position 1: " + $this->position1->toString();
		}

		if($this->position2 != null){
			$lines[] = "Position 2: " + $this->position2->toString();
		}

		return $lines;
	}

	public function getArea() : int{
		if($this->position1 == null){
			return -1;
		}

		if($this->position2 == null){
			return -1;
		}

		return $this->region->getArea();
	}
}