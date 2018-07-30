<?php

namespace edit\functions\pattern;

use pocketmine\block\Block;

use edit\blocks\BaseBlock;
use edit\Vector;

class RandomPattern extends AbstractPattern{

	private $patterns = [];
	private $max = 0;

	public function __construct(){
	}

	public function add(Pattern $pattern, float $chance){
		$this->patterns[] = new Chance($pattern, $chance);
		$this->max += $chance;
	}

	public function apply(Vector $position) : BaseBlock{
		$r = rand(0, 1000) / 1000;
		$offset = 0;

		foreach($this->patterns as $chance){
			if($r <= ($offset + $chance->getChance()) / $this->max){
				return $chance->getPattern()->apply($position);
			}
			$offset += $chance->getChance();
		}

		echo("error");
	}
}
class Chance {
	private $pattern;
	private $chance;

	public function __construct(Pattern $pattern, float $chance){
		$this->pattern = $pattern;
		$this->chance = $chance;
        }

	public function getPattern() : Pattern{
		return $this->pattern;
	}

	public function getChance() : float{
		return $this->chance;
        }
}