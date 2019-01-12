<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\session\ClipboardHolder;
use edit\extent\clipboard\BlockArrayClipboard;
use edit\functions\block\BlockReplace;
use edit\functions\operation\ForwardExtentCopy;
use edit\functions\operation\Operations;
use edit\command\util\FlagChecker;
use edit\command\util\HelpChecker;
use edit\command\util\DefinedChecker;
use edit\command\util\SpaceChecker;

class CutCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"選択している範囲をクリップボードにカットします",
			"//copy"
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
			$sender->sendMessage("§c効果: §a選択している範囲をクリップボードにカットします\n".
					     "§c使い方: §a//cut\n".
					     "§cフラグ: §a-e: エンティティーもコピーします\n".
					     "§c      : §a-m: -----------");
			return false;
		}

		if(DefinedChecker::checkPosition($sender)) {
			return false;
		}

		$check = FlagChecker::check($args);

        $args = $check[0];
        $flags = $check[1];

		$copyEntities = false;

		foreach($flags as $flag){
			switch($flag){
				case "e":
					$copyEntities = true;
					break;
				case "m":
					break;
			}
		}

		$region = Main::getInstance()->getEditSession($sender)->getRegionSelector($sender->getLevel())->getRegion();

		$clipboard = new BlockArrayClipboard($region);
		$clipboard->setOrigin(Main::getInstance()->getEditSession($sender)->getPlacementPosition($sender));
		$copy = new ForwardExtentCopy(Main::getInstance()->getEditSession($sender), $region, $region->getMinimumPoint(), $clipboard, $region->getMinimumPoint());
		$leavePattern = Main::getInstance()->getPatternFactory()->parseFromInput("0");
		$copy->setSourceFunction(new BlockReplace(Main::getInstance()->getEditSession($sender), $leavePattern));
		$copy->setCopyingEntities($copyEntities);
		$copy->setRemovingEntities(true);
		//if ($mask != null) {
		//	$copy->setSourceMask($mask);
		//}
		Operations::completeLegacy($copy);
		Main::getInstance()->getEditSession($sender)->setClipboard(new ClipboardHolder($clipboard, Main::getInstance()->getEditSession($sender)->getWorld()));
		Main::getInstance()->getEditSession($sender)->remember();

		$sender->sendMessage(Main::LOGO.$region->getArea()."ブロックをコピーしました");
		return true;
	}
}