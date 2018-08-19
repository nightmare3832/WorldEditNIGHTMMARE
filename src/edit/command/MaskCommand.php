<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\functions\mask\BlockMask;

class MaskCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"ブラシのフィルターを設定します",
			"//mask [フィルター]"
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
			$sender->sendMessage("§c効果: §aブラシのフィルターを設定します\n".
					     "§c使い方: §a//mask [フィルター]");
			return false;
		}

		if(count($args) < 1){
			Main::getInstance()->getBrushTool($sender->getInventory()->getItemInHand(), $sender)->setMask(null);
			$sender->sendMessage(Main::LOGO."ブラシのフィルターが無効になりました");
		}else{
			$items = explode(",", $args[0]);
			$blocks = [];

			foreach($items as $item){
				$blocks[] = Main::getInstance()->getBlockFactory()->parseFromInput($item);
			}

			$session = Main::getInstance()->getEditSession($sender);
			$mask = new BlockMask($session, $blocks);

			Main::getInstance()->getBrushTool($sender->getInventory()->getItemInHand(), $sender)->setMask($mask);

			$sender->sendMessage(Main::LOGO."ブラシのフィルターを設定しました");
		}
		return true;
	}
}