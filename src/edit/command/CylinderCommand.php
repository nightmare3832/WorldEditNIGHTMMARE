<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\functions\pattern\Pattern;
use edit\command\util\FlagChecker;

class CylinderCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"柱体を生成します",
			"//cylinder <ブロックパターン> <半径>[,<半径>], <高さ>"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		$copyEntities = false;

		$check = FlagChecker::check($args);

		$args = $check[0];
		$flags = $check[1];

		if(count($args) < 3){
			$sender->sendMessage("§c使い方: //cylinder <ブロックパターン> <半径>[,<半径>], <高さ>");
			return true;
		}

		$hollow = false;

		foreach($flags as $flag){
			switch($flag){
				case "h":
					$hollow = true;
					break;
			}
		}

		$radii = explode(",", $args[1]);

		switch(count($radii)){
			case 1:
				$radiusX = $radiusY = $radiusZ = (float) $radii[0];
				break;
			case 2:
				$radiusX = (float) $radii[0];
				$radiusZ = (float) $radii[1];
				break;
			default:
				$sender->sendMessage("半径の値が足りません");
				return true;
		}

		$session = Main::getInstance()->getEditSession($sender);

		$pos = $session->getPlacementPosition($sender);

		$fill = Main::getInstance()->getPatternFactory()->parseFromInput($args[0]);
		$height = (int) $args[2];
		$affected = $session->makeCylinder($pos, $fill, $radiusX,$radiusZ, $height, !$hollow);
		$session->remember();
		Main::findFreePosition($sender);
		$sender->sendMessage(Main::LOGO.$affected."ブロックを設置しました");
		return true;
	}
}