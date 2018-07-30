<?php

namespace edit\session;

use pocketmine\level\Level;

use edit\extent\Extent;
use edit\extent\clipboard\Cipboard;
use edit\math\transform\Transform;
use edit\math\transform\Identity;
use edit\extent\clipboard\Clipboard;

class ClipboardHolder{

	private $world;
	private $clipboard;
	private $transform;

	public function __construct(Clipboard $clipboard, Level $worldData){
		$this->clipboard = $clipboard;
		$this->worldData = $worldData;
		$this->transform = new Identity();
	}

	public function getWorldData() : Level{
		return $this->worldData;
	}

	public function getClipboard() : Clipboard{
		return $this->clipboard;
	}

	public function setTransform(Transform $transform){
		$this->transform = $transform;
	}

	public function getTransform() : Transform{
		return $this->transform;
	}

	public function createPaste(Extent $targetExtent, Level $targetWorldData) : PasteBuilder{
		return new PasteBuilder($this, $targetExtent, $targetWorldData);
	}
}