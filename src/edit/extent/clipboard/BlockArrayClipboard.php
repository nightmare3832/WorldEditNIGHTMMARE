<?php

namespace edit\extent\clipboard;

use pocketmine\block\Block;
use pocketmine\entity\Entity;

use edit\blocks\BaseBlock;
use edit\util\Location;
use edit\regions\Region;
use edit\functions\operation\Operation;
use edit\Vector;

class BlockArrayClipboard implements Clipboard{

	private $region;
	private $origin;
	private $blocks;
	private $entities = [];

	public function __construct(Region $region){
		$this->region = clone $region;
		$this->origin = $region->getMinimumPoint();

		$blocks = [];
	}

	public function getRegion() : Region{
		return clone $this->region;
	}

	public function getOrigin() : Vector{
		return $this->origin;
	}

	public function setOrigin(Vector $origin){
		$this->origin = $origin;
	}

	public function getDimensions() : Vector{
		return $this->region->getMaximumPoint()->subtract($this->region->getMinimumPoint())->add(1, 1, 1);
	}

	public function getMinimumPoint() : Vector{
		return $this->region->getMinimumPoint();
	}

	public function getMaximumPoint() : Vector{
		return $this->region->getMaximumPoint();
	}

	public function getEntities(?Region $region = null) : array{
		if($region == null) return $this->entities;
		$filtered = [];
		foreach($this->entities as $clipboardEntity){
			$entity = $clipboardEntity->getEntity();
			$pt = new Vector($entity->x, $entity->y, $entity->z);
			if($region->contains($pt)) $filtered[] = $entity;
		}
		return $filtered;
	}

	public function createEntity(Location $location, Entity $entity) : ?Entity{
		$ret = new ClipboardEntity($location, $entity);
		$this->entities[] = $ret;
		return $entity;
	}

	public function getBlock(Vector $position) : BaseBlock{
		if($this->region->contains($position)){
			$v = $position->subtract($this->region->getMinimumPoint());
			$block = $this->blocks[$v->getBlockX()][$v->getBlockY()][$v->getBlockZ()];
			if($block != null){
				return clone $block;
			}
		}

		return Block::get(Block::AIR);
	}

	public function getLazyBlock(Vector $position) : Block{
		return $this->getBlock($position);
	}

	public function setBlock(Vector $position, $block) : bool{
		if($this->region->contains($position)){
			$v = $position->subtract($this->region->getMinimumPoint());
			$this->blocks[$v->getBlockX()][$v->getBlockY()][$v->getBlockZ()] = clone $block;
			return true;
		}else{
			return false;
		}
	}

	//public function getBiome(Vector2D $position) : BaseBiome{
	//	  return new BaseBiome(0);
	//}

	//public function setBiome(Vector2D $position, BaseBiome $biome) : bool{
	//	  return false;
	//}

	public function commit() : ?Operation{
		return null;
	}

}