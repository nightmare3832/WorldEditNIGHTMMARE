<?php

namespace edit\blocks;

use pocketmine\block\Block;

use edit\jnbt\CompoundTag;
use edit\jnbt\StringTag;
use edit\CuboidClipboard;

class BaseBlock implements TileEntityBlock{

	const MAX_ID = 4095;

	const MAX_DATA = 15;

	private $id;
	private $data;

	private $nbtData = null;

	public function __construct(int $id, ?int $data = 0, ?CompoundTag $nbtData = null){
		$this->setId($id);
		$this->setData($data);
		$this->setNbtData($nbtData);
	}

	public function getId() : int{
		return $this->id;
	}

	public function internalSetId(int $id){
		if($id > self::MAX_ID){
			return;
		}

		if($id < 0){
			return;
		}

		$this->id = $id;
	}

	public function setId(int $id){
		$this->internalSetId($id);
	}

	public function getData() : int{
		return $this->data;
	}

	public function internalSetData(int $data){
		if($data > self::MAX_DATA){
			return;
		}

		if($data < -1){
			return;
		}

		$this->data = $data;
	}

	public function setData(int $data){
		$this->internalSetData($data);
	}

	public function setIdAndData(int $id, int $data){
		$this->setId($id);
		$this->setData($data);
	}

	public function hasWildcardData() : bool{
		return $this->getData() == -1;
	}

	public function hasNbtData() : bool{
		return $this->getNbtData() != null;
	}

	public function getNbtId() : string{
		$nbtData = $this->getNbtData();
		if($nbtData == null){
			return "";
		}
		$idTag = $nbtData->getValue()->get("id");
		if($idTag != null && $idTag instanceof StringTag){
			return $idTag->getValue();
		}else{
			return "";
		}
	}

	public function getNbtData() : ?CompoundTag{
		return $this->nbtData;
	}

	public function setNbtData(?CompoundTag $nbtData){
		$this->nbtData = $nbtData;
	}

	public function getType() : int{
		return $this->getId();
	}

	public function setType(int $type){
		$this->setId($type);
	}

	public function isAir() : bool{
		return $this->getType() == Block::AIR;
	}

	public function rotate90() : int{
		$newData = BlockData::rotate90($this->getType(), $this->getData());
		$this->setData($newData);
		return $newData;
	}

	public function rotate90Reverse() : int{
		$newData = BlockData::rotate90Reverse($this->getType(), $this->getData());
		$this->setData($newData);
		return $newData;
	}

	public function cycleData(int $increment) : int{
		$newData = BlockData::cycle($this->getType(), $this->getData(), $increment);
		$this->setData($newData);
		return $newData;
	}

	public function flip(int $direction = null) : BaseBlock{
		if($direction == null) $this->setData(BlockData::flip($this->getType(), $this->getData(), null));
		else $this->setData(BlockData::flip($this->getType(), $this->getData(), $direction));
		return $this;
	}

	public function equals($o) : bool{
		if(!($o instanceof BaseBlock)){
			return false;
		}

		return $this->getType() == $o->getType() && $this->getData() == $o->getData();
	}

	public function equalsFuzzy(BaseBlock $o){
		return ($this->getType() == $o->getType()) && ($this->getData() == $o->getData() || $this->getData() == -1 || $o->getData() == -1);
	}

	public function inIterable(array $iter) : bool{
		foreach($iter as $block){
			if($block->equalsFuzzy($this)){
				return true;
			}
		}
		return false;
	}

	public static function containsFuzzy(array $collection, BaseBlock $o) : bool{
		return Blocks::containsFuzzy($collection, $o);
	}
}