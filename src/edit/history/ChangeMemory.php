<?php

namespace edit\history;

use edit\history\change\Change;

class ChangeMemory{

	private $changes = [];

	public function __construct(){
	}

	public function add(Change $change){
		$this->changes[] = $change;
	}

	public function undo($session){
		foreach($this->changes as $change){
			$change->undo($session);
		}
		$session->reorderUndo();
	}

	public function redo($session){
		foreach($this->changes as $change){
			$change->redo($session);
		}
		$session->reorderUndo();
	}
}