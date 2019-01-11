<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\command\util\SpaceChecker;

class Pos1Command extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"ひとつめのポジションを設定します",
			"//pos1"
		);
		//$data = new CommandManager($this);
		//$data->createCommandData();
		//$data->setSubCommand(0, false, ["all"]);
		//$data->setSubCommand(1, false, ["party"]);
		//$data->setSubCommand(2, false, ["clan"]);
		//$data->register();
		//$this->setPermission("");
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

		if(isset($args[0])  && (!SpaceChecker::check($args))){
			$p = explode(",", $args[0]);
			$pos = new Vector($p[0], $p[1], $p[2]);
		}else{
			$pos = Main::getInstance()->getEditSession($sender)->getPlacementPosition($sender);
		}

		Main::getInstance()->getEditSession($sender)->getRegionSelector($sender->getLevel())->selectPrimary($pos);
		Main::getInstance()->getEditSession($sender)->getRegionSelector($sender->getLevel())->explainPrimarySelection($sender);
		return true;
	}
}