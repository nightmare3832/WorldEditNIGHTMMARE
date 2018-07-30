<?php

namespace edit\util;

use pocketmine\Player;
use pocketmine\block\Block;

use edit\Vector;
use edit\BlockWorldVector;

class TargetBlock{

	private $world;
	private $maxDistance;
	private $checkDistance;
	private $curDistance;
	private $targetPos;
	private $targetPosDouble;
	private $prevPos;
	private $offset;

	public function __construct(Player $player, int $maxDistance, float $checkDistance){
		$this->world = $player->getLevel();
		$this->setValues(new Vector($player->x, $player->y, $player->z), $player->yaw, $player->pitch, $maxDistance, 1.65, $checkDistance);
	}

	private function setValues(Vector $loc, float $xRotation, float $yRotation, int $maxDistance, float $viewHeight, float $checkDistance){
		$this->maxDistance = $maxDistance;
		$this->checkDistance = $checkDistance;
		$this->curDistance = 0;
		$xRotation = ($xRotation + 90) % 360;
		$yRotation = $yRotation * -1;

		$h = ($checkDistance * cos(deg2rad($yRotation)));

		$this->offset = new Vector(($h * cos(deg2rad($xRotation))),
					($checkDistance * sin(deg2rad($yRotation))),
					($h * sin(deg2rad($xRotation))));

		$this->targetPosDouble = $loc->add(0, $viewHeight, 0);
		$this->targetPos = $this->targetPosDouble->toBlockPoint();
		$this->prevPos = $this->targetPos;
	}

	public function getAnyTargetBlock() : ?BlockWorldVector{
		$searchForLastBlock = true;
		$lastBlock = null;
		while($this->getNextBlock() != null){
			if($this->world->getBlock($this->getCurrentBlock()->toVector3())->getId() == Block::AIR){
				if($searchForLastBlock){
					$lastBlock = $this->getCurrentBlock();
					if($lastBlock->getBlockY() <= 0 || $lastBlock->getBlockY() >= 255){
						$searchForLastBlock = false;
					}
				}
			}else{
				break;
			}
		}
		$currentBlock = $this->getCurrentBlock();
		return ($currentBlock != null ? $currentBlock : $lastBlock);
	}

	public function getTargetBlock() : ?BlockWorldVector{
		while($this->getNextBlock() != null && $this->world->getBlock($this->getCurrentBlock()->toVector3())->getId() == Block::AIR);
		return $this->getCurrentBlock;
	}

	public function getSolidTargetBlock() : ?BlockWorldVector{
		while($this->getNextBlock() != null && $this->world->getBlock($this->getCurrentBlock()->toVector3())->canPassThrough());
		return $this->getCurrentBlock;
	}

	public function getNextBlock() : ?BlockWorldVector{
		$this->prevPos = $this->targetPos;
		do{
			$this->curDistance += $this->checkDistance;

			$this->targetPosDouble = $this->offset->add($this->targetPosDouble->getX(),
								$this->targetPosDouble->getY(),
								$this->targetPosDouble->getZ());
			$this->targetPos = $this->targetPosDouble->toBlockPoint();
		}while($this->curDistance <= $this->maxDistance
			&& $this->targetPos->getBlockX() == $this->prevPos->getBlockX()
			&& $this->targetPos->getBlockY() == $this->prevPos->getBlockY()
			&& $this->targetPos->getBlockZ() == $this->prevPos->getBlockZ());

		if($this->curDistance > $this->maxDistance){
			return null;
		}

		return new BlockWorldVector($this->world, $this->targetPos);
	}

	public function getCurrentBlock() : ?BlockWorldVector{
		if($this->curDistance > $this->maxDistance){
			return null;
		}else{
			return new BlockWorldVector($this->world, $this->targetPos);
		}
	}
}