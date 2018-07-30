<?php

namespace edit\functions\entity;

use pocketmine\entity\Entity;

use edit\jnbt\CompoundTag;
use edit\jnbt\CompoundTagBuilder;
use edit\blocks\BaseBlock;
use edit\extent\Extent;
use edit\functions\EntityFunction;
use edit\math\transform\Transform;
use edit\internal\helper\MCDirections;
use edit\history\change\EntityRemove;
use edit\util\Direction;
use edit\util\DirectionFlag;
use edit\util\Location;
use edit\Vector;
use edit\EditSession;

class ExtentEntityCopy implements EntityFunction{

	private $destination;
	private $from;
	private $to;
	private $transform;
	private $removing;

	public function __construct(Vector $from, Extent $destination, Vector $to, Transform $transform){
		$this->from = $from;
		$this->destination = $destination;
		$this->to = $to;
		$this->transform = $transform;
	}

	public function isRemoving() : bool{
		return $this->removing;
	}

	public function setRemoving(bool $removing){
		$this->removing = $removing;
	}

	public function apply(Entity $entity) : bool{
		$location = new Location($this->destination, new Vector($entity->x, $entity->y, $entity->z), $entity->getYaw(), $entity->getPitch());
		$pivot = $this->from->round()->add(0.5, 0.5, 0.5);

		$newPosition = $this->transform->apply($location->toVector()->subtract($pivot));
		$newDirection = $this->transform->isIdentity() ?
			$location->getDirection()
			: $this->transform->apply($location->getDirection())->subtract($this->transform->apply(new Vector(0, 0, 0)))->normalize();
		$newLocation = new Location($this->destination, $newPosition->add($this->to->round()->add(0.5, 0.5, 0.5)), $newDirection->toYaw(), $newDirection->toPitch());

		$entity = $this->transformNbtData($entity);

		$success = $this->destination->createEntity($newLocation, $entity) != null;

		if($this->isRemoving() && $success){
			if($this->destination instanceof EditSession) $this->destination->changeMemory->add(new EntityRemove($newLocation, $entity));
			$entity->close();
		}

		return $success;
	}

	private function transformNbtData(Entity $state) : Entity{
		return $state;
	}
}