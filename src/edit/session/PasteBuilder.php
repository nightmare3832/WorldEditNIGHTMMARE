<?php

namespace edit\session;

use pocketmine\level\Level;

use edit\extent\Extent;
use edit\Vector;
use edit\extent\transform\BlockTransformExtent;
use edit\functions\mask\ExistingBlockMask;
use edit\functions\operation\ForwardExtentCopy;
use edit\functions\operation\Operation;
use edit\Main;

class PasteBuilder{

	private $clipboard;
	private $worldData;
	private $transform;
	private $targetExtebt;
	private $targetWorldData;

	private $to;
	private $ignoreAirBlocks;

	public function __construct(ClipboardHolder $holder, Extent $targetExtent, Level $targetWorldData){
		$this->clipboard = $holder->getClipboard();
		$this->worldData = $holder->getWorldData();
		$this->transform = $holder->getTransform();
		$this->targetExtent = $targetExtent;
		$this->targetWorldData = $targetWorldData;
		$this->to = new Vector(0, 0, 0);
	}

	public function to(Vector $to) : PasteBuilder{
		$this->to = $to;
		return $this;
	}

	public function ignoreAirBlocks(bool $ignoreAirBlocks) : PasteBuilder{
		$this->ignoreAirBlocks = $ignoreAirBlocks;
		return $this;
	}

	public function build() : Operation{
		$extent = new BlockTransformExtent($this->clipboard, $this->transform, Main::getInstance()->getBlockRegistry());//.getBlockRegistry()
		$copy = new ForwardExtentCopy($extent, $this->clipboard->getRegion(), $this->clipboard->getOrigin(), $this->targetExtent, $this->to);
		$copy->setTransform($this->transform);
		if($this->ignoreAirBlocks){
			$copy->setSourceMask(new ExistingBlockMask($this->clipboard));
		}
		return $copy;
	}
}