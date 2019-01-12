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
use edit\command\util\HelpChecker;
use edit\command\util\DefinedChecker;
use edit\regions\selector\CuboidRegionSelector;
use edit\command\util\SpaceChecker;

class SaveCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"クリップボードを保存します",
			"//save"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if(HelpChecker::check($args) || SpaceChecker::check($args)){
			$sender->sendMessage("§c効果: §aクリップボードを保存します\n".
					     "§c使い方: §a//save <名前>");
			return false;
		}

		if(DefinedChecker::checkClipboard($sender)) {
			return false;
		}

		if(count($args) >= 1){
			$session = Main::getInstance()->getEditSession($sender);

			$holder = $session->getClipboard();
			$clipboard = $holder->getClipboard();
			$transform = $holder->getTransform();
			$target = null;

			//if(!$transform->isIdentity()){
				
			//}else{
				$target = $clipboard;
			//}

			$text = serialize($target);
			file_put_contents(Main::$clipboardDirectory."/".$args[0].".clipboard", $text);
			
			/**
			 * FileWriteTaskで非同期書き込み
			 * 処理内容はfile_put_contents()と一緒。非同期で行う
			 * $task = new FileWriteTask(Main::$clipboardDirectory."/".$args[0].".clipboard",$text);
			 * Main::getInstance()->getServer()->getAsyncPool()->submitTask($task);
			 */
			 
			$sender->sendMessage(Main::LOGO."クリップボードを保存しました\n".Main::$clipboardDirectory."/".$args[0].".clipboard");
		}else{
			$sender->sendMessage(Main::LOGO."名前を指定してください\n§c使い方: §a//save <名前>");
		}

		return true;
	}
}