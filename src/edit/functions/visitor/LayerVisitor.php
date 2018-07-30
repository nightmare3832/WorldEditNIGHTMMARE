<?php

namespace edit\functions\visitor;

use edit\Vector;
use edit\regions\FlatRegion;
use edit\functions\operation\RunContext;
use edit\functions\operation\Operation;
use edit\functions\LayerFunction;
use edit\functions\mask\Mask2D;
use edit\functions\mask\Masks;

class LayerVisitor implements Operation{

	private $flatRegion;
	private $function1;
	private $mask;
	private $minY;
	private $maxY;

	public function __construct(FlatRegion $flatRegion, int $minY, int $maxY, LayerFunction $function1){
		$this->flatRegion = $flatRegion;
		$this->minY = $minY;
		$this->maxY = $maxY;
		$this->function1 = $function1;

		$this->mask = Masks::alwaysTrue2D();
	}

	public function getMask() : Mask2D{
		return $this->mask;
	}

	public function setMask(Mask2D $mask){
		$this->mask = $mask;
	}

	public function resume(RunContext $run) : ?Operation{
		foreach($this->flatRegion->asFlatRegion() as $column){
			if(!$this->mask->test($column)){
				continue;
			}

			if($this->function1->isGround($column->toVector($this->maxY + 1))){
				return null;
			}

			$found = false;
			$groundY = 0;
			for($y = $this->maxY;$y >= $this->minY;$y--){
				$test = $column->toVector($y);
				if(!$found){
					if($this->function1->isGround($test)){
						$found = true;
						$groundY = $y;
					}
				}

				if($found){
					if(!$this->function1->apply($test, $groundY - $y)){
						break;
					}
				}
			}
		}

		return null;
	}

	public function cancel(){
	}

	public function addStatusMessages(array $messages){
	}
}