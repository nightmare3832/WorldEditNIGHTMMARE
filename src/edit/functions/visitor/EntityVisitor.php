<?php

namespace edit\functions\visitor;

use edit\regions\Region;
use edit\functions\operation\RunContext;
use edit\functions\operation\Operation;
use edit\functions\EntityFunction;

class EntityVisitor implements Operation{

	private $entities;
	private $function1;
	private $affected = 0;

	public function __construct(array $entities, EntityFunction $function1){
		$this->entities = $entities;
		$this->function1 = $function1;
	}

	public function getAffected() : int{
		return $this->affected;
	}

	public function resume(RunContext $run) : ?Operation{
		foreach($this->entities as $entity){
			if($this->function1->apply($entity)){
				$this->affected++;
			}
		}

		return null;
	}

	public function cancel(){
	}

	public function addStatusMessages(array $messages){
		$messages[] = $this->getAffected() . " entities affected";
	}
}