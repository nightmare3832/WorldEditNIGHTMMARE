<?php

namespace edit\history\change;

use edit\BlockVector;
use edit\blocks\BaseBlock;

class BlockChange implements Change{

	private $position;
	private $previous;
	private $current;

	public function __construct(BlockVector $position, BaseBlock $previous, BaseBlock $current){
		$this->position = $position;
		$this->previous = $previous;
		$this->current = $current;
	}

	public function getPosition() : BlockVector{
		return $this->position;
	}

	public function getPrevious() : BaseBlock{
		return $this->previous;
	}

	public function getCurrent() : BaseBlock{
		return $this->current;
	}

	public function undo($session){
		$session->setBlockUndo($this->position, $this->previous);
	}

	public function redo($session){
		$session->setBlockUndo($this->position, $this->current);
	}

}