<?php

namespace edit\world\registry;

use edit\Vector;

class BundledBlockData{

	private $idMap = [];
	private $legacyMap = [];

	private static $instance;

	public function __construct(){
		self::$instance = $this;
		$this->loadFromResource();
	}

	private function loadFromResource(){
		$path = __FILE__ ;
		$dir = dirname($path);
		$entries = json_decode(file_get_contents($dir."/blocks.json"));
		foreach($entries as $entry){
			$e = new BlockEntry();
			$e->legacyId = $entry->legacyId;
			$e->id = $entry->id;
			$e->unlocalizedName = $entry->unlocalizedName;
			$e->states = [];
			if(isset($entry->states)){
				foreach($entry->states as $n => $s){
					$simpleState = new SimpleState();
					$simpleState->dataMask = $s->dataMask;
					$simpleState->values = [];
					foreach($s->values as $k => $v){
						$simpleStateValue = new SimpleStateValue();
						$simpleStateValue->data = $v->data;
						if(isset($v->direction)){
							$simpleStateValue->direction = new Vector($v->direction[0], $v->direction[1], $v->direction[2]);
						}else{
							$simpleStateValue->direction = null;
						}
						$simpleState->values[$k] = $simpleStateValue;
					}
					$e->states[] = $simpleState;
				}
			}
			$blockMaterial = new SimpleBlockMaterial();
			if(isset($entry->material)){
				$blockMaterial->renderedAsNormalBlock = $entry->material->renderedAsNormalBlock;
				$blockMaterial->fullCube = $entry->material->fullCube;
				$blockMaterial->opaque = $entry->material->opaque;
				$blockMaterial->powerSource = $entry->material->powerSource;
				$blockMaterial->liquid = $entry->material->liquid;
				$blockMaterial->solid = $entry->material->solid;
				$blockMaterial->hardness = $entry->material->hardness;
				$blockMaterial->resistance = $entry->material->resistance;
				$blockMaterial->slipperiness = $entry->material->slipperiness;
				$blockMaterial->grassBlocking = $entry->material->grassBlocking;
				$blockMaterial->ambientOcclusionLightValue = $entry->material->ambientOcclusionLightValue;
				$blockMaterial->lightOpacity = $entry->material->lightOpacity;
				$blockMaterial->lightValue = $entry->material->lightValue;
				$blockMaterial->fragileWhenPushed = $entry->material->fragileWhenPushed;
				$blockMaterial->unpushable = $entry->material->unpushable;
				$blockMaterial->adventureModeExempt = $entry->material->adventureModeExempt;
				$blockMaterial->ticksRandomly = $entry->material->ticksRandomly;
				$blockMaterial->usingNeighborLight = $entry->material->usingNeighborLight;
				$blockMaterial->movementBlocker = $entry->material->movementBlocker;
				$blockMaterial->burnable = $entry->material->burnable;
				$blockMaterial->toolRequired = $entry->material->toolRequired;
				$blockMaterial->replacedDuringPlacement = $entry->material->replacedDuringPlacement;
			}
			$e->material = $blockMaterial;
			$e->postDeserialization();
			$this->idMap[$e->id] = $e;
			$this->legacyMap[$e->legacyId] = $e;
		}
	}

	private function findById($id) : ?BlockEntry{
		if(is_numeric($id)){
			if(isset($this->legacyMap[$id])){
				return $this->legacyMap[$id];
			}else{
				return null;
			}
		}else{
			if(isset($this->idMap[$id])){
				return $this->idMap[$id];
			}else{
				return null;
			}
		}
	}

	public function toLegacyId($id) : ?int{
		$entry = $this->findById($id);
		if($entry != null){
			return $entry->legacyId;
		}else{
			return null;
		}
	}

	public function getMaterialById($id) : ?BlockMaterial{
		$entry = $this->findById($id);
		if($entry != null){
			return $entry->material;
		}else{
			return null;
		}
	}

	public function getStatesById($id) : ?array{
		$entry = $this->findById($id);
		if($entry != null){
			return $entry->states;
		}else{
			return null;
		}
	}

	public static function getInstance() : BundledBlockData{
		return self::$instance;
	}
}