<?php

namespace edit\extension\factory;

use pocketmine\item\Item;
use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\internal\registry\InputParser;
use edit\functions\pattern\BlockPattern;
use edit\Main;

class DefaultBlockParser extends InputParser{

	public function __construct(){
	}

	public function parseFromInput(string $input){
		$b = explode(":", str_replace([" ", "minecraft:"], ["_", ""], trim($input)));
		if(!isset($b[1])){
			$meta = 0;
		}else{
			$meta = $b[1] & 0xFFFF;
		}

		if(defined(Item::class . "::" . strtoupper($b[0]))){
			$item = Item::get(constant(Item::class . "::" . strtoupper($b[0])), $meta);
			if($item->getId() === Item::AIR and strtoupper($b[0]) !== "AIR"){
				$item = Item::get($b[0] & 0xFFFF, $meta);
			}
		}else{
			$item = Item::get($b[0] & 0xFFFF, $meta);
		}

		return new BaseBlock($item->getId(), $item->getDamage());
	}

}