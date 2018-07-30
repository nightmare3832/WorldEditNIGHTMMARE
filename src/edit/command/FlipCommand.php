<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\functions\operation\Operations;
use edit\command\util\FlagChecker;
use edit\math\transform\AffineTransform;

class FlipCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"クリップボードをひっくり返します",
			"//flip [<方向>]"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if(count($args) < 1){
			$direction = Main::getCardinalDirection($sender);
		}else{
			$direction = Main::getFlipDirection($sender, $args[0]);
		}

		$session = Main::getInstance()->getEditSession($sender);

		$holder = $session->getClipboard();
		$transform = new AffineTransform();
		$transform = $transform->scale($direction->positive()->multiply(-2)->add(1, 1, 1));
		$holder->setTransform($holder->getTransform()->combine($transform));

		$sender->sendMessage(Main::LOGO."クリップボードをひっくり返しました");
		return true;
	}
}