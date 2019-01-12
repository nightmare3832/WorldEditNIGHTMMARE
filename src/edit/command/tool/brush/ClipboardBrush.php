<?php

namespace edit\command\tool\brush;

use edit\Vector;
use edit\EditSession;
use edit\WorldVector;
use edit\functions\pattern\Pattern;
use edit\functions\operation\Operations;
use edit\session\ClipboardHolder;

class ClipboardBrush implements Brush{

	private $holder;
	private $ignoreAirBlocks;
	private $usingOrigin;

	public function __construct(ClipboardHolder $holder, bool $ignoreAirBlocks, bool $usingOrigin){
		$this->holder = $holder;
		$this->ignoreAirBlocks = $ignoreAirBlocks;
		$this->usingOrigin = $usingOrigin;
	}

	public function build(EditSession $editSession, Vector $position, ?Pattern $pattern, float $size){
		if (empty($this->holder)) return;
		$clipboard = $this->holder->getClipboard();
		$region = $clipboard->getRegion();
		$centerOffset = $region->getCenter()->subtract($clipboard->getOrigin());

		$operation = $this->holder->createPaste($editSession, $editSession->getPlayer()->getLevel())
				->to($this->usingOrigin ? $position : $position->subtract($centerOffset))
				->ignoreAirBlocks($this->ignoreAirBlocks)
				->build();

		Operations::completeLegacy($operation);
	}
}
