<?php

namespace edit\functions\operation;

use edit\regions\Region;
use edit\extent\Extent;
use edit\Vector;
use edit\math\transform\Identity;
use edit\math\transform\Transform;
use edit\functions\RegionFunctions;
use edit\functions\mask\Mask;
use edit\functions\mask\Masks;
use edit\functions\RegionFunction;
use edit\functions\CombinedRegionFunction;
use edit\functions\RegionMaskingFilter;
use edit\functions\block\ExtentBlockCopy;
use edit\functions\entity\ExtentEntityCopy;
use edit\functions\visitor\EntityVisitor;
use edit\functions\visitor\RegionVisitor;

class ForwardExtentCopy implements Operation{

	private $source;
	private $destination;
	private $region;
	private $from;
	private $to;
	private $repetitions = 1;
	private $sourceMask;
	private $removingEntities = false;
	private $copyingEntities = true;
	private $sourceFunction = null;
	private $transform;
	private $currentTransform = null;
	private $lastVisitor;
	private $affected = 0;

	public function __construct(Extent $source, Region $region, Vector $from, Extent $destination, Vector $to){
		$this->source = $source;
		$this->destination = $destination;
		$this->region = $region;
		$this->from = $from;
		$this->to = $to;
		$this->sourceMask = Masks::alwaysTrue();
		$this->transform = new Identity();
	}

	public function getTransform() : Transform{
		return $this->transform;
	}

	public function setTransform(Transform $transform){
		$this->transform = $transform;
	}

	public function getSourceMask() : Mask{
		return $this->sourceMask;
	}

	public function setSourceMask(Mask $sourceMask){
		$this->sourceMask = $sourceMask;
	}

	public function getSourceFunction() : RegionFunction{
		return $this->sourceFunction;
	}

	public function setSourceFunction(RegionFunction $function1){
		$this->sourceFunction = $function1;
	}

	public function getRepetitions() : int{
		return $this->repetitions;
	}

	public function setRepetitions(int $repetitions){
		$this->repetitions = $repetitions;
	}

	public function isCopyingEntities() : bool{
		return $this->copyingEntities;
	}

	public function setCopyingEntities(bool $copyingEntities){
		$this->copyingEntities = $copyingEntities;
	}

	public function isRemovingEntities() : bool{
		return $this->removingEntities;
	}

	public function setRemovingEntities(bool $removingEntities){
		$this->removingEntities = $removingEntities;
	}

	public function getAffected() : int{
		return $this->affected;
	}

	public function resume(RunContext $run) : ?Operation{
		if($this->lastVisitor != null){
			$this->affected += $this->lastVisitor->getAffected();
			$this->lastVisitor = null;
		}

		if($this->repetitions > 0){
			$this->repetitions--;

			if($this->currentTransform == null){
				$this->currentTransform = $this->transform;
			}

			$blockCopy = new ExtentBlockCopy($this->source, $this->from, $this->destination, $this->to, $this->currentTransform);
			$filter = new RegionMaskingFilter($this->sourceMask, $blockCopy);
			$function1 = $this->sourceFunction != null ? new CombinedRegionFunction([$filter, $this->sourceFunction]) : $filter;
			$blockVisitor = new RegionVisitor($this->region, $function1);

			$this->lastVisitor = $blockVisitor;
			$this->currentTransform = $this->currentTransform->combine($this->transform);

			if($this->copyingEntities){
				$entityCopy = new ExtentEntityCopy($this->from, $this->destination, $this->to, $this->currentTransform);
				$entityCopy->setRemoving($this->removingEntities);
				$entities = $this->source->getEntities($this->region);
				$entityVisitor = new EntityVisitor($entities, $entityCopy);
				return new DelegateOperation($this, new OperationQueue([$blockVisitor, $entityVisitor]));
			}else{
				return new DelegateOperation($this, $blockVisitor);
			}
		}else{
			return null;
		}
	}

	public function cancel(){
	}

	public function addStatusMessages(array $messages){
	}
}