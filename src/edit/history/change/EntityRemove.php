<?php

namespace edit\history\change;

use pocketmine\entity\Entity;

use edit\util\Location;

class EntityRemove implements Change{

	private $location;
	private $entity;

	public function __construct(Location $location, Entity $entity){
		$this->location = $location;
		$this->entity = $entity;
	}

	public function undo($session){
		$this->entity = $session->createEntity($this->location, $this->entity);
	}

	public function redo($session){
		if($this->entity !== null){
			$this->entity->close();
		}
	}

}