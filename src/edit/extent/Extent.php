<?php

namespace edit\extent;

use pocketmine\entity\Entity;

use edit\Vector;
use edit\regions\Region;
use edit\util\Location;

interface Extent extends InputExtent, OutputExtent{

    function getMinimumPoint() : Vector;

    function getMaximumPoint() : Vector;

    function getEntities(?Region $region) : array;

    function createEntity(Location $location, Entity $entity) : ?Entity;

}