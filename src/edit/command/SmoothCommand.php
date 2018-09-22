<?php

declare(strict_types=1);

namespace edit\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;

use edit\Vector;
use edit\Main;
use edit\functions\pattern\Pattern;
use edit\math\convolution\HeightMap;
use edit\math\convolution\HeightMapFilter;
use edit\math\convolution\GaussianKernel;
use edit\command\util\HelpChecker;
use edit\command\util\DefinedChecker;

class SmoothCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"選択した範囲のブロックを滑らかにします",
			"//smooth <回数>"
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
			$sender->sendMessage("§c効果: §a選択した範囲のブロックを滑らかにします\n".
					     "§c使い方: §a//smooth <回数>");
			return false;
		}

		if(DefinedChecker::checkPosition($sender)) {
			return false;
		}

		if(empty($args[0])) $args[0] = 1;

		$session = Main::getInstance()->getEditSession($sender);
		$region = $session->getRegionSelector($sender->getLevel())->getRegion();
		$heightMap = new HeightMap($session, $region);
		$filter = new HeightMapFilter(new GaussianKernel(5, 1.0));
		$affected = $heightMap->applyFilter($filter, $args[0]);
		$sender->sendMessage(Main::LOGO.$affected."ブロックを設置しました");
		return true;
	}
}