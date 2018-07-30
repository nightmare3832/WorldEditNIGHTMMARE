<?php

namespace edit\world\registry;

class BlockEntry{

	public $legacyId;
	public $id;
	public $unlocalizedName;
	public $aliases;
	public $states;
	public $material;

	function postDeserialization(){
		foreach($this->states as $state){
			$state->postDeserialization();
		}
	}
}