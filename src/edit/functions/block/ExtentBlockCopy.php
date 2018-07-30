<?php

namespace edit\functions\block;

use edit\jnbt\CompoundTag;
use edit\jnbt\CompoundTagBuilder;
use edit\blocks\BaseBlock;
use edit\extent\Extent;
use edit\functions\RegionFunction;
use edit\math\transform\Transform;
use edit\internal\helper\MCDirections;
use edit\util\Direction;
use edit\util\DirectionFlag;
use edit\Vector;

class ExtentBlockCopy implements RegionFunction{

	private $source;
	private $destination;
	private $from;
	private $to;
	private $transform;

	public function __construct(Extent $source, Vector $from, Extent $destination, Vector $to, Transform $transform){
		$this->source = $source;
		$this->from = $from;
		$this->destination = $destination;
		$this->to = $to;
		$this->transform = $transform;
	}

	public function apply(Vector $position) : bool{
		$block = $this->source->getBlock($position);
		$orig = $position->subtract($this->from);
		$transformed = $this->transform->apply($orig);

		$block = $this->transformNbtData($block);

		return $this->destination->setBlock($transformed->add($this->to), $block);
	}

	private function transformNbtData(BaseBlock $state) : BaseBlock{
		$tag = $state->getNbtData();

		if($tag != null){
			if($tag->containsKey("Rot")){
				$rot = $tag->asInt("Rot");

				$direction = MCDirections::fromRotation($rot);

				if($direction != null){
					$vector = $this->transform->apply($direction->toVector())->subtract($this->transform->apply(new Vector(0, 0, 0)))->normalize();
					$newDirection = Direction::findClosest($vector, DirectionFlag::CARDINAL | DirectionFlag::ORDINAL | DirectionFlag::SECONDARY_ORDINAL);

					if($newDirection != null){
						$builder = $tag->createBuilder();

						$builder->putByte("Rot", MCDirections::toRotation($newDirection));

						return new BaseBlock($state->getId(), $state->getData(), $builder->build());
					}
				}
			}
		}

		return $state;
	}
}