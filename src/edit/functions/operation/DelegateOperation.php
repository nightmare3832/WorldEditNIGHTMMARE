<?php

namespace edit\functions\operation;

class DelegateOperation implements Operation{

	private $original;
	private $delegate;

	public function __construct(Operation $original, Operation $delegate){
		$this->original = $original;
		$this->delegate = $delegate;
	}

	public function resume(RunContext $run) : ?Operation{
		$this->delegate = $this->delegate->resume($run);
		return $this->delegate != null ? $this : $this->original;
	}

	public function cancel(){
		$this->delegate->cancel();
		$this->original->cancel();
	}

	public function addStatusMessages(array $messages){
		$this->original->addStatusMessages($messages);
		$this->delegate->addStatusMessages($messages);
	}

}
