<?php

namespace edit\command\tool\brush;

use pocketmine\block\Block;

use edit\Vector;
use edit\EditSession;
use edit\WorldVector;
use edit\blocks\BaseBlock;
use edit\functions\pattern\Pattern;

class GravityBrush implements Brush{

	private $fullHeight;

	public function __construct(bool $fullHeight){
		$this->fullHeight = $fullHeight;
	}

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size){
		$air = new BaseBlock(Block::AIR, 0);
		$startY = $this->fullHeight ? 255 : $position->getBlockY() + $size;
		for($x = $position->getBlockX() + $size;$x > $position->getBlockX() - $size;--$x){
			for($z = $position->getBlockZ() + $size;$z > $position->getBlockZ() - $size;--$z){
				$y = $startY;
				$blockTypes = [];
				for(;$y > $position->getBlockY() - $size;--$y){
					$pt = new Vector($x, $y, $z);
					$block = $editSession->getBlock($pt);
					if($block->getId() != Block::AIR){
						$blockTypes[] = $block;
						$editSession->setBlock($pt, $air);
					}
				}
				$pt = new Vector($x, $y, $z);
				$blockTypes = array_reverse($blockTypes);
				for($i = 0;$i < count($blockTypes);){
					if($editSession->getBlock($pt)->getId() == Block::AIR){
						$editSession->setBlock($pt, $blockTypes[$i++]);
					}
					$pt = $pt->add(0, 1, 0);
				}
			}
		}
	}
}