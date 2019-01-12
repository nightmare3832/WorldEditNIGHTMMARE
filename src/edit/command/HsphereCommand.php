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
use edit\command\util\HelpChecker;
use edit\command\util\SpaceChecker;

class HsphereCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"空洞の球体を生成します",
			"//hsphere <ブロックパターン> <半径>[,<半径>,<半径>] [頭上?]"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if(!Main::$canUseNotOp && !$sender->isOp()){
			return false;
		}

		if(HelpChecker::check($args) || SpaceChecker::check($args)){
			$sender->sendMessage("§c効果: §a球体を生成します\n".
					     "§c使い方: §a//hsphere <ブロックパターン> <半径>[,<半径>,<半径>] [頭上?]\n");
			return false;
		}

		$copyEntities = false;

		$check = FlagChecker::check($args);

		$args = $check[0];
		$flags = $check[1];

		if(count($args) < 2){
			$sender->sendMessage("§c使い方: //hsphere <ブロックパターン> <半径>[,<半径>,<半径>] [頭上?]");
			return true;
		}

		$radii = explode(",", $args[1]);

		switch(count($radii)){
			case 1:
				$radiusX = $radiusY = $radiusZ = (float) $radii[0];
				break;
			case 3:
				$radiusX = (float) $radii[0];
				$radiusY = (float) $radii[1];
				$radiusZ = (float) $radii[2];
				break;
			default:
				$sender->sendMessage("半径の値が足りません");
				return true;
		}

		$session = Main::getInstance()->getEditSession($sender);

		//$raised = 

		$pos = $session->getPlacementPosition($sender);
		//if($raised){
		//	$pos = $pos->add(0, $radiusY, 0);
		//}

		$fill = Main::getInstance()->getPatternFactory()->parseFromInput($args[0]);

		$affected = $session->makeSphere($pos, $fill, $radiusX, $radiusY, $radiusZ, false);
		$session->remember();
		Main::findFreePosition($sender);
		$sender->sendMessage(Main::LOGO.$affected."ブロックを生成しました");
		Main::getInstance()->getServer()->broadcastMessage("§7".Main::LOGO.$sender->getName()." が /".$this->getName()." を利用");
		return true;
	}
}