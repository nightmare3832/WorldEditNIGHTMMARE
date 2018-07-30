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

class SetCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"選択した範囲を指定したブロックに置換します",
			"//set <ブロックパターン>"
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

		if(count($args) < 1){
			return true;
		}

		$pattern = Main::getInstance()->getPatternFactory()->parseFromInput($args[0]);

		$session = Main::getInstance()->getEditSession($sender);

		$affected = $session->setBlocks($session->getRegionSelector($sender->getLevel())->getRegion()->iterator(), $pattern);
		echo($affected);
		$session->remember();
		$sender->sendMessage(Main::LOGO.$affected."ブロックを設置しました");
		return true;
	}
}