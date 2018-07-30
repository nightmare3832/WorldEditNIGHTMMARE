<?php

namespace edit\blocks;

use edit\world\NbtValued;

interface TileEntityBlock extends NbtValued{

	function getNbtId() : string;

}