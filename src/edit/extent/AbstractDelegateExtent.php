<?php

namespace edit\extent;

use pocketmine\entity\Entity;

use edit\Vector;
use edit\Vector2D;
use edit\blocks\BaseBlock;
use edit\functions\operation\Operation;
use edit\functions\operation\OperationQueue;
use edit\util\Location;
use edit\regions\Region;

abstract class AbstractDelegateExtent implements Extent{

	private $extent;

	public function __construct(Extent $extent){
		$this->extent = $extent;
	}

	public function getExtent() : Extent{
		return $this->extent;
	}

	public function getBlock(Vector $position) : BaseBlock{
		return $this->extent->getBlock($position);
	}

	public function getLazyBlock(Vector $position) : BaseBlock{
		return $this->extent->getLazyBlock($position);
	}

	public function setBlock(Vector $location, $block) : bool{
		return $this->extent->setBlock($location, $block);
	}

	public function createEntity(Location $location, Entity $entity) : ?Entity{
		return $this->extent->createEntity($location, $entity);
	}

	public function getEntities(?Region $region = null) : array{
		return $this->extent->getEntities($region);
	}

	public function getMinimumPoint() : Vector{
		return $this->extent->getMinimumPoint();
	}

	public function getMaximumPoint() : Vector{
		return $this->extent->getMaximumPoint();
	}

	protected function commitBefore() : ?Operation{
		return null;
	}

	public function commit() : ?Operation{
		$ours = $this->commitBefore();
		$other = $this->extent->commit();
		if($ours != null && $other != null){
			return new OperationQueue($ours, $other);
		}else if($ours != null){
			return $ours;
		}else if($other != null){
			return $other;
		}else{
			return null;
		}
	}
}