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
use edit\functions\pattern\Pattern;
use edit\command\tool\brush\SphereBrush;
use edit\command\tool\brush\SmoothBrush;
use edit\command\tool\brush\HollowSphereBrush;
use edit\command\tool\brush\ClipboardBrush;
use edit\command\tool\brush\CylinderBrush;
use edit\command\tool\brush\ClipboardBrush;
use edit\command\tool\brush\HollowCylinderBrush;
use edit\command\tool\brush\GravityBrush;
use edit\command\util\FlagChecker;

class BrushCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"ブラシを選択します",
			"//brush <sphere|cylinder|clipboard|smooth|gravity>"
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!($sender instanceof Player)){
			return true;
		}

		$copyEntities = false;

		if(count($args) >= 1){
			$check = FlagChecker::check($args);

			$args = $check[0];
			$flags = $check[1];

			switch($args[0]){
				case "sphere":
				case "s":
					if(count($args) < 2){
						$sender->sendMessage("§c使い方: §a//brush sphere <ブロックパターン> [半径]".
								     "§cフラグ: §a-h: 空洞にします");
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

					if(empty($args[2])) $args[2] = 2;

					$fill = Main::getInstance()->getPatternFactory()->parseFromInput($args[1]);

					$this->sphereBrush($sender, $fill, (float) $args[2], $hollow);
					break;
				case "cylinder":
				case "cyl":
				case "c":
					if(count($args) < 2){
						$sender->sendMessage("§c使い方: §a//brush cylinder <ブロックパターン> [半径] [高さ]".
								     "§cフラグ: §a-h: 空洞にします");
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

					if(empty($args[2])) $args[2] = 2;
					if(empty($args[3])) $args[3] = 1;

					$fill = Main::getInstance()->getPatternFactory()->parseFromInput($args[1]);

					$this->cylinderBrush($sender, $fill, (float) $args[2], (int) $args[3], $hollow);
					break;
				case "clipboard":
				case "copy":

					$this->clipboardBrush($sender, false, false);
					break;
				case "smooth":
					if(count($args) < 3){
						$sender->sendMessage("§c使い方: §a//brush smooth [サイズ] [回数]".
								     "§cフラグ: §a-n: 自然のブロックのみに適応します");
						return true;
					}

					$naturalBlocksOnly = false;

					foreach($flags as $flag){
						switch($flag){
							case "n":
								$naturalBlocksOnly = true;
								break;
						}
					}

					$this->smoothBrush($sender, (float) $args[1], (int) $args[2], $naturalBlocksOnly);
					break;
				case "gravity":
				case "grav":
					if(count($args) < 2){
						$sender->sendMessage("§c使い方: §a//brush gravity [半径]".
								     "§cフラグ: §a-h: 高さ無制限");
						return true;
					}

					$fromMaxY = false;

					foreach($flags as $flag){
						switch($flag){
							case "h":
								$fromMaxY = true;
								break;
						}
					}

					$this->gravityBrush($sender, (float) $args[1], $fromMaxY);
					break;
				default:
					break;
			}
		}else{
			$sender->sendMessage("§a//brush sphere: §7球体を生成します\n".
					     "§a//brush cylinder: §7円柱を生成します\n".
					     "§a//brush clipboard: §7クリップボードを貼り付けます\n".
					     "§a//brush smooth: §7地形を滑らかにします\n".
					     "§a//brush gravity: §7重力を発生します\n");
		}
		return true;
	}

	public function sphereBrush(Player $player, Pattern $fill, float $radius, bool $hollow){
		//checkMaxBrushRadius

		$tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
		$tool->setFill($fill);
		$tool->setSize($radius);
		if($hollow){
			$tool->setBrush(new HollowSphereBrush());
		}else{
			$tool->setBrush(new SphereBrush());
		}

		$player->sendMessage(Main::LOGO."球体のブラシを選択しました (".$radius.")");
	}

	public function cylinderBrush(Player $player, Pattern $fill, float $radius, int $height, bool $hollow){
		//checkMaxBrushRadius

		$tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
		$tool->setFill($fill);
		$tool->setSize($radius);
		if($hollow){
			$tool->setBrush(new HollowCylinderBrush($height));
		}else{
			$tool->setBrush(new CylinderBrush($height));
		}

		$player->sendMessage(Main::LOGO."円柱のブラシを選択しました (".$radius." by ".$height.")");
	}

	public function clipboardBrush(Player $player, bool $ignoreAir, bool $usingOrigin){
		//checkMaxBrushRadius

		$holder = Main::getInstance()->getEditSession($player)->getClipboard();
		if(!($holder instanceof ClipboardHolder)){
			$player->sendMessage(Main::LOGO."クリップボードに何もありません");
			return;
		}
		$clipboard = $holder->getClipboard();
		$size = $clipboard->getDimensions();

		$tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
		$tool->setSize($size->getBlockX());
		$tool->setBrush(new ClipboardBrush($holder, $ignoreAir, $usingOrigin));

		$player->sendMessage(Main::LOGO."クリップボードブラシを選択しました");
	}

	public function smoothBrush(Player $player, float $radius, int $iterations, bool $naturalBlocksOnly){
		//checkMaxBrushRadius

		$tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
		$tool->setSize($radius);
		$tool->setBrush(new SmoothBrush($iterations, $naturalBlocksOnly));

		$player->sendMessage(Main::LOGO."スムーズブラシを選択しました (".$radius." x ".$iterations.", using ".($naturalBlocksOnly ? "自然のブロックのみ" : "すべてのブロック").")");
	}

	public function gravityBrush(Player $player, float $radius, bool $fromMaxY){
		//checkMaxBrushRadius

		$tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
		$tool->setSize($radius);
		$tool->setBrush(new GravityBrush($fromMaxY));

		$player->sendMessage(Main::LOGO."重力ブラシを選択しました (".$radius.")");
	}
}