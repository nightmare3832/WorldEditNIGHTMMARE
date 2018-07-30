<?php

namespace edit\world;

use edit\jnbt\CompoundTag;

interface NbtValued{

	public function hasNbtData() : bool;

	function getNbtData() : ?CompoundTag;

	function setNbtData(?CompoundTag $nbtData);

}