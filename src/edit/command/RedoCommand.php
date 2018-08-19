<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;

class RedoCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"‚â‚è’¼‚·",
			"//redo [‰ñ”]"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if($args[0] === "help"){
			$sender->sendMessage("˜cŒø‰Ê: ˜a‚â‚è’¼‚µ‚Ü‚·\n".
					     "˜cŽg‚¢•û: ˜a//redo [‰ñ”]");
			return false;
		}

		$session = Main::getInstance()->getEditSession($sender);

		$session->redo();
		$sender->sendMessage(Main::LOGO."‚â‚è’¼‚µ‚µ‚Ü‚µ‚½");
		return true;
	}
}