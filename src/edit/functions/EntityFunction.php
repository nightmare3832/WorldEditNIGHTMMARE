<?php

namespace edit\functions;

use pocketmine\entity\Entity;

interface EntityFunction{

	public function apply(Entity $entity) : bool;

}