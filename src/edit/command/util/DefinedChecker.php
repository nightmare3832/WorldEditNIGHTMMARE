<?php

namespace edit\command\util;

use pocketmine\Player;

use edit\Main;
use edit\command\tool\brush\HollowCylinderBrush;
use edit\command\tool\brush\CylinderBrush;
use edit\command\tool\brush\SphereBrush;
use edit\functions\pattern\Pattern;

class DefinedChecker{

    public static function checkPosition(Player $player) : bool{
        if(!Main::getInstance()->getEditSession($player)->getRegionSelector($player->getLevel())->isDefined()) {
            $player->sendMessage(Main::LOGO."座標がセットされていません");
            return true;
        }
        return false;
    }

    public static function checkClipboard(Player $player) : bool{
        if(Main::getInstance()->getEditSession($player)->getClipboard() == null) {
            $player->sendMessage(Main::LOGO."クリップボードが設定されていません");
            return true;
        }
        return false;
    }

    public static function checkBrush(Player $player) {
        $tool = Main::getInstance()->getBrushTool($player->getInventory()->getItemInHand(), $player);
        $brush = $tool->getBrush();
        if($brush instanceof SphereBrush or $brush instanceof CylinderBrush or $brush instanceof HollowCylinderBrush) {
            if(!$tool->getMaterial() instanceof Pattern) {
                return true;
            }
        }
        return false;
    }
}