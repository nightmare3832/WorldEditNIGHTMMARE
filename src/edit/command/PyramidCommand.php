<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\command\util\FlagChecker;
use edit\command\util\HelpChecker;

class PyramidCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"ピラミッドを生成します",
			"//pyramid <ブロックパターン> <サイズ>"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		if(HelpChecker::check($args)){
			$sender->sendMessage("§c効果: §aピラミッドを生成します\n".
					     "§c使い方: §a//pyramid <ブロックパターン> <サイズ>\n".
					     "§cフラグ: §a-h: 空洞にします");
			return false;
		}

		$check = FlagChecker::check($args);

		$args = $check[0];
		$flags = $check[1];

		if(count($args) < 2){
			$sender->sendMessage("§c使い方: §a//pyramid <ブロックパターン> <サイズ>");
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

		$session = Main::getInstance()->getEditSession($sender);


		$pos = $session->getPlacementPosition($sender);

		$fill = Main::getInstance()->getPatternFactory()->parseFromInput($args[0]);

		$affected = $session->makePyramid($pos, $fill, (int) $args[1], !$hollow);
		$session->remember();
		Main::findFreePosition($sender);
		$sender->sendMessage(Main::LOGO.$affected."ブロックを生成しました");
		return true;
	}
}