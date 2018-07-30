<?php

namespace edit\world\registry;

use edit\blocks\BaseBlock;

interface State{

	function valueMap(): array;

	function getValue(BaseBlock $block) : ?StateValue;

	function hasDirection() : bool;

}
