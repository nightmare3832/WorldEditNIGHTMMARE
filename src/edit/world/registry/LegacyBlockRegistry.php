<?php

namespace edit\world\registry;

use edit\blocks\BaseBlock;
use edit\blocks\BlockMaterial;

class LegacyBlockRegistry implements BlockRegistry {

	public function createFromId($id) : BaseBlock{
		if(is_numeric($id)) return new BaseBlock($id);
		$legacyId = BundledBlockData::getInstance()::toLegacyId($id);
		if($legacyId != null){
			return $this->createFromId($legacyId);
		} else {
			return null;
		}
	}

	public function getMaterial(BaseBlock $block) : ?BlockMaterial{
		return BundledBlockData::getInstance()::getMaterialById($block->getId());
	}

	public function getStates(BaseBlock $block) : ?array{
		return BundledBlockData::getInstance()->getStatesById($block->getId());
	}

}