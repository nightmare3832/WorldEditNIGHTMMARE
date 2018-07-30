<?php

namespace edit\extent\clipboard;

use pocketmine\entity\Entity;

use edit\util\Location;

class ClipboardEntity{

	private $location;
	private $entity;

	public function __construct(Location $location, Entity $entity){
		$this->location = $location;
		$this->entity = $entity;
	}

	public function getEntity() : Entity{
		return $this->entity;
	}

	public function getLocation() : Location{
		return $this->location;
	}
}