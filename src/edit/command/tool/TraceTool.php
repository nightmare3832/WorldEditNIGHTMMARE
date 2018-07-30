<?php

namespace edit\command\tool;

use pocketmine\Player;

interface TraceTool extends Tool {

	public function actPrimary(Player $player) : bool;
}