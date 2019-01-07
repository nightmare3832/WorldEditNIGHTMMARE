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

class PasteCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"クリップボードを貼り付けます",
			"//paste"
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
			$sender->sendMessage("§c効果: §aクリップボードを貼り付けます\n".
					     "§c使い方: §a//paste\n".
					     "§cフラグ: §a-a: 空気ブロックを無視します\n".
					     "§c      : §a-o: コピー元を原点とします\n".
					     "§c      : §a-s: コピー後の範囲を選択します");
			return false;
		}

		if(DefinedChecker::checkClipboard($sender)) {
			return false;
		}

		$check = FlagChecker::check($args);

		$args = $check[0];
		$flags = $check[1];

		$ignoreAirBlocks = false;
		$atOrigin = false;
		$selectPasted = false;

		foreach($flags as $flag){
			switch($flag){
				case "a":
					$ignoreAirBlocks = true;
					break;
				case "o":
					$atOrigin = true;
					break;
				case "s":
					$selectPasted = true;
					break;
			}
		}

		$session = Main::getInstance()->getEditSession($sender);

		$holder = $session->getClipboard();
		$clipboard = $holder->getClipboard();
		$region = $clipboard->getRegion();

		$to = $atOrigin ? $clipboard->getOrigin() : new Vector($sender->x, $sender->y, $sender->z);
		$operation = $holder
			->createPaste($session, $session->getWorld())
			->to($to)
			->ignoreAirBlocks($ignoreAirBlocks)
			->build();
		Operations::completeLegacy($operation);

		if($selectPasted){
			$clipboardOffset = $clipboard->getRegion()->getMinimumPoint()->subtract($clipboard->getOrigin());
			$realTo = $to->add($holder->getTransform()->apply($clipboardOffset));
			$max = $realTo->add($holder->getTransform()->apply($region->getMaximumPoint()->subtract($region->getMinimumPoint())));
			$selector = new CuboidRegionSelector($sender->getLevel(), $realTo, $max);
			$session->setRegionSelector($sender->getLevel(), $selector);
			$selector->learnChanges();
			$selector->explainRegionAdjust($sender);
		}
		$session->remember();

		$sender->sendMessage(Main::LOGO.$to->floor()->toString()."に貼り付けました");
		return true;
	}
}