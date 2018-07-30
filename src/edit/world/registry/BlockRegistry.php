<?php

namespace edit\world\registry;

use edit\blocks\BaseBlock;
use edit\blocks\BlockMaterial;

interface BlockRegistry {

	function createFromId($id) : BaseBlock;//string

	function getMaterial(BaseBlock $block) : ?BlockMaterial;

	function getStates(BaseBlock $block) : ?array;

}