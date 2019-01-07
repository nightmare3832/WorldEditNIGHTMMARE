<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\blocks\BaseBlock;
use edit\functions\operation\Operations;
use edit\command\util\FlagChecker;
use edit\command\util\HelpChecker;
use edit\command\util\DefinedChecker;
use edit\math\transform\AffineTransform;
use edit\command\util\SpaceChecker;

class StackCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"選択した範囲を繰り返します",
			"//stack [回数] [方向]"
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
			$sender->sendMessage("§c効果: §a選択した範囲を繰り返します\n".
					     "§c使い方: §a//stack [回数] [方向]\n".
					     "§cフラグ: §a-s: 移動後の範囲を選択します\n".
					     "§c      : §a-a: 空気ブロックを無視します");
			return false;
		}

		if(DefinedChecker::checkPosition($sender)) {
			return false;
		}

		$check = FlagChecker::check($args);

        $args = $check[0];
        $flags = $check[1];

		$moveSelection = false;
		$ignoreAirBlocks = false;

		foreach($flags as $flag){
			switch($flag){
				case "s":
					$moveSelection = true;
					break;
				case "a":
					$ignoreAirBlocks = true;
					break;
			}
		}

		$count = 1;
		if(isset($args[0])) $count = (int) $args[0];

		if(count($args) < 2){
			$direction = Main::getCardinalDirection($sender);
		}else{
			$direction = Main::getFlipDirection($sender, $args[1]);
		}

		$session = Main::getInstance()->getEditSession($sender);

		$affected = $session->stackCuboidRegion($session->getRegionSelector($sender->getLevel())->getRegion(), $direction, $count, !$ignoreAirBlocks);
		$session->remember();
		$sender->sendMessage(Main::LOGO.$affected."ブロックを生成しました");
		return true;
	}
}