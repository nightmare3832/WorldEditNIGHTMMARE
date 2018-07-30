<?php

namespace edit\command\tool;

use pocketmine\Player;

use edit\Vector;
use edit\command\tool\brush\Brush;
use edit\command\tool\brush\SphereBrush;
use edit\command\tool\brush\SmoothBrush;
use edit\command\tool\brush\GravityBrush;
use edit\functions\mask\Mask;
use edit\functions\mask\MaskIntersection;
use edit\functions\pattern\Pattern;
use edit\Main;

class BrushTool implements TraceTool{

	const MAX_RANGE = 500;

	protected $range = -1;
	private $mask = null;
	private $brush;
	private $material;
	private $size = 1;

	public function __construct(){
		$this->brush = new SphereBrush();
	}

	public function canUse(Player $player) : bool{
		return true;
	}

	public function getMask() : ?Mask{
		return $this->mask;
	}

	public function setMask(?Mask $filter){
		$this->mask = $filter;
	}

	public function setBrush(Brush $brush){
		$this->brush = $brush;
	}

	public function getBrush() : Brush{
		return $this->brush;
	}

	public function setFill(Pattern $pattern){
		$this->material = $pattern;
	}

	public function getMaterial() : Pattern{
		return $this->material;
	}

	public function getSize() : float{
		return $this->size;
	}

	public function setSize(float $size){
		$this->size = $size;
	}

	public function getRange() : int{
		return ($this->range < 0) ? self::MAX_RANGE : min($this->range, self::MAX_RANGE);
	}

	public function setRange(int $range){
		$this->range = $range;
	}

	public function actPrimary(Player $player) : bool{
		$target = null;
		$target = Main::getBlockTrace($player, $this->getRange(), true);

		if($target == null){
			$player->sendMessage("No block in sight!");
			return true;
		}
		if(!($this->getBrush() instanceof SmoothBrush) && !($this->getBrush() instanceof GravityBrush)){
			$dir = $player->getDirectionVector();
			$target = new Vector($player->x + (($dir->x * 2) * $this->getSize()), $player->y + (($dir->y * 2) * $this->getSize()) + $player->getEyeHeight(), $player->z + (($dir->z * 2) * $this->getSize()));
		}

		$editSession = Main::getInstance()->getEditSession($player);

		$existingMask = null;
		if($this->mask != null){
			$existingMask = $editSession->getMask();

			if($existingMask == null) {
				$editSession->setMask([$this->mask]);
			}else if($existingMask instanceof MaskIntersection){
				$existingMask->add($this->mask);
			}else{
				$newMask = new MaskIntersection([$existingMask]);
				$newMask->add([$this->mask]);
				$editSession->setMask($newMask);
			}
		}
		$this->brush->build($editSession, $target, $this->material, $this->size);

		if($existingMask != null) $editSession->setMask($existingMask);

		$editSession->remember();
		return true;
	}
}