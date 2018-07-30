<?php

namespace edit\math\convolution;

use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\EditSession;
use edit\regions\Region;
use edit\Vector;

class HeightMap{

	private $data;
	private $width;
	private $height;

	private $region;
	private $session;

	public function __construct(EditSession $session, Region $region, bool $naturalOnly = false){
		$this->session = $session;
		$this->region = $region;

		$this->width = $region->getWidth();
		$this->height = $region->getHeight();

		$minX = $region->getMinimumPoint()->getBlockX();
		$minY = $region->getMinimumPoint()->getBlockY();
		$minZ = $region->getMinimumPoint()->getBlockZ();
		$maxY = $region->getMaximumPoint()->getBlockY();

		$this->data = [];
		for($z = 0;$z < $this->height;++$z){
			for($x = 0;$x < $this->width;++$x){
				$this->data[$z * $this->width + $x] = $session->getHighestTerrainBlock($x + $minX, $z + $minZ, $minY, $maxY, $naturalOnly);
			}
		}
	}

	public function applyFilter(HeightMapFilter $filter, int $iterations) : int{
		$newData = [];
		for($i = 0;$i < count($this->data);$i++){
			$newData[$i] = $this->data[$i];
		}

		for($i = 0;$i < $iterations;++$i){
			$newData = $filter->filter($newData, $this->width, $this->height);
		}

		return $this->apply($newData);
	}

	public function apply(array $data2) : int{
		$minY = $this->region->getMinimumPoint();
		$originX = $minY->getBlockX();
		$originY = $minY->getBlockY();
		$originZ = $minY->getBlockZ();

		$maxY = $this->region->getMaximumPoint()->getBlockY();
		$fillerAir = new BaseBlock(Block::AIR);

		$blockChanged = 0;

		for($z = 0;$z < $this->height;++$z){
			for($x = 0;$x < $this->width;++$x){
				$index = $z * $this->width + $x;
				$curHeight = $this->data[$index];

				$newHeight = min($maxY, $data2[$index]);

				$xr = $x + $originX;
				$zr = $z + $originZ;

				if(($newHeight - $originY) != 0) $scale = (float) ($curHeight - $originY) / (float) ($newHeight - $originY);
				$scale = 1;

				if($newHeight > $curHeight){
					$existing = $this->session->getBlock(new Vector($xr, $curHeight, $zr));

					if($existing->getId() != Block::WATER && $existing->getId() != Block::STILL_WATER
						&& $existing->getId() != Block::LAVA && $existing->getId() != Block::STILL_LAVA){
						$this->session->setBlock(new Vector($xr, $newHeight, $zr), $existing);
						++$blockChanged;

						for($y = $newHeight - 1 - $originY;$y >= 0;--$y){
							$copyFrom = (int) ($y * $scale);
							$this->session->setBlock(new Vector($xr, $originY + $y, $zr), $this->session->getBlock(new Vector($xr, $originY + $copyFrom, $zr)));
							++$blockChanged;
						}
					}
				}else if($curHeight > $newHeight){
					for($y = 0;$y < $newHeight - $originY;++$y){
						$copyFrom = (int) ($y * $scale);
						$this->session->setBlock(new Vector($xr, $originY + $y, $zr), $this->session->getBlock(new Vector($xr, $originY + $copyFrom, $zr)));
						++$blockChanged;
					}

					$this->session->setBlock(new Vector($xr, $newHeight, $zr), $this->session->getBlock(new Vector($xr, $curHeight, $zr)));
					++$blockChanged;

					for($y = $newHeight + 1;$y <= $curHeight;++$y){
						$this->session->setBlock(new Vector($xr, $y, $zr), $fillerAir);
						++$blockChanged;
					}
				}
			}
		}

		return $blockChanged;
	}
}