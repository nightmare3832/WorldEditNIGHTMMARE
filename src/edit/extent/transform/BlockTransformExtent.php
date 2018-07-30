<?php

namespace edit\extent\transform;

use edit\Vector;
use edit\blocks\BaseBlock;
use edit\extent\AbstractDelegateExtent;
use edit\extent\Extent;
use edit\math\transform\Transform;
use edit\world\registry\BlockRegistry;
use edit\world\registry\State;
use edit\world\registry\StateValue;

class BlockTransformExtent extends AbstractDelegateExtent{

	//const RIGHT_ANGLE = deg2rad(90);

	private $transform;
	private $blockRegistry;

	public function __construct(Extent $extent, Transform $transform, BlockRegistry $blockRegistry){
		parent::__construct($extent);
		$this->transform = $transform;
		$this->blockRegistry = $blockRegistry;
	}

	public function getTransform() : Transform{
		return $this->transform;
	}

	private function transformBlock(BaseBlock $block, bool $reverse) : BaseBlock{
		self::transform($block, $reverse ? $this->transform->inverse() : $this->transform, $this->blockRegistry);
		return $block;
	}

	public function getBlock(Vector $position) : BaseBlock{
		return $this->transformBlock(parent::getBlock($position), false);
	}

	public function getLazyBlock(Vector $position) : BaseBlock{
		return $this->transformBlock(parent::getLazyBlock($position), false);
	}

	public function setBlock(Vector $location, $block) : bool{
		return parent::setBlock($location, $this->transformBlock(clone $block, true));
	}

	private static function transform(BaseBlock $block, Transform $transform, BlockRegistry $registry, ?BaseBlock $changedBlock = null) : BaseBlock{
		if($changedBlock == null) $changedBlock = $block;

		$states = $registry->getStates($block);

		if($states == null){
			return $changedBlock;
		}

		foreach($states as $state){
			if($state->hasDirection()){
				$value = $state->getValue($block);
				if($value != null && $value->getDirection() != null){
					$newValue = self::getNewStateValue($state, $transform, $value->getDirection());
					if($newValue != null){
						$newValue->set($changedBlock);
					}
				}
			}
		}

		return $changedBlock;
	}

	private static function getNewStateValue(State $state, Transform $transform, Vector $oldDirection) : StateValue{
		$newDirection = $transform->apply($oldDirection)->subtract($transform->apply(new Vector(0, 0, 0)))->normalize();
		$newValue = null;
		$closest = -2;
		$found = false;

		foreach($state->valueMap() as $v){
			if($v->getDirection() != null){
				$dot = $v->getDirection()->normalize()->dot($newDirection);
				if($dot >= $closest){
					$closest = $dot;
					$newValue = $v;
					$found = true;
				}
			}
		}

		if($found){
			return $newValue;
		}else{
			return null;
		}
	}
}