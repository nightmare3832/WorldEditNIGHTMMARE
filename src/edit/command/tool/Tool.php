<?php

namespace edit\command\tool;

use pocketmine\Player;

interface Tool {

	public function canUse(Player $player) : bool;

}