<?php

namespace edit\functions\visitor;

use edit\regions\Region;
use edit\functions\operation\RunContext;
use edit\functions\operation\Operation;
use edit\functions\RegionFunction;

class RegionVisitor implements Operation{

	private $region;
	private $function1;
	private $affected = 0;

	public function __construct(Region $region, RegionFunction $function1){
		$this->region = $region;
		$this->function1 = $function1;
	}

	public function getAffected() : int{
		return $this->affected;
	}

	public function resume(RunContext $run) : ?Operation{
		foreach($this->region->iterator() as $pt){
			if($this->function1->apply($pt)){
				$this->affected++;
			}
		}

		return null;
	}

	public function cancel(){
	}

	public function addStatusMessages(array $messages){
		$messages[] = $this->getAffected() . " blocks affected";
	}
}