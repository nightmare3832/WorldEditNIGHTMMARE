<?php

namespace edit\functions\operation;

class OperationQueue implements Operation{

	private $operations = [];
	private $queue = [];
	private $current = null;

	public function __construct(array $operations){
		foreach($operations as $operation){
			$this->offer($operation);
		}
		$this->operations = $operations;
	}

	public function offer(Operation $operation){
		$this->queue[] = $operation;
	}

	public function resume(RunContext $run) : ?Operation{
		if($this->current == null && count($this->queue) > 0){
			$this->current = $this->queue[0];
			unset($this->queue[0]);
			$this->queue = array_values($this->queue);
		}

		if($this->current != null){
			$this->current = $this->current->resume($run);

			if($this->current == null){
				if(empty($this->queue[0])){
					$this->current = null;
				}else{
					$this->current = $this->queue[0];
					unset($this->queue[0]);
					$this->queue = array_values($this->queue);
				}
			}
		}

		return $this->current != null ? $this : null;
	}

	public function cancel(){
		foreach($this->queue as $operation){
			$operation->cancel();
		}
		$this->queue = [];
	}

	public function addStatusMessages(array $messages){
		foreach($this->operations as $operation){
			$operation->addStatusMessages($messages);
		}
	}
}