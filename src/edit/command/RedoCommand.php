<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\command\util\HelpChecker;
use edit\command\util\SpaceChecker;

class RedoCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"やり直す",
			"//redo [回数]"
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
			$sender->sendMessage("§c効果: §aやり直します\n".
					     "§c使い方: §a//redo [回数]");
			return false;
		}

		$session = Main::getInstance()->getEditSession($sender);

		$session->redo();
		$sender->sendMessage(Main::LOGO."やり直ししました");
		Main::getInstance()->getServer()->broadcastMessage("§7".Main::LOGO.$sender->getName()." が /".$this->getName()." を利用");
		return true;
	}
}