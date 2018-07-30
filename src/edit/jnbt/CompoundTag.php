<?php

namespace edit\jnbt;

class CompoundTag extends Tag{

	private $value;

	public function __construct(array $value){
		parent::__construct();
		$this->value = $value;
	}

	public function containsKey(string $key) : bool{
		return isset($this->value[$key]);
	}

	public function getValue(){
		return $this->value;
	}

	public function setValue(array $value) : CompoundTag{
		return new CompoundTag($value);
	}

	public function createBuilder() : CompoundTagBuilder{
		return new CompoundTagBuilder($ths->value);
	}

	public function getByteArray(string $key) : array{
		$tag = $this->value[$key];
		if($tag instanceof ByteArrayTag){
			return $tag->getValue();
		}else{
			return [];
		}
	}

	public function getByte(string $key) : int{
		$tag = $this->value[$key];
		if($tag instanceof ByteTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getDouble(string $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof DoubleTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function asDouble(stirng $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof ByteTag){
			return $tag->getValue();
		}else if($tag instanceof ShortTag){
			return $tag->getValue();
		}else if($tag instanceof IntTag){
			return $tag->getValue();
		}else if($tag instanceof LongTag){
			return $tag->getValue();
		}else if($tag instanceof FloatTag){
			return $tag->getValue();
		}else if($tag instanceof DoubleTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getFloat(string $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof FloatTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getIntArray(string $key) : array{
		$tag = $this->value[$key];
		if($tag instanceof IntArrayTag){
			return $tag->getValue();
		}else{
			return [];
		}
	}

	public function getInt(string $key) : int{
		$tag = $this->value[$key];
		if($tag instanceof IntTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function asInt(stirng $key) : int{
		$tag = $this->value[$key];
		if($tag instanceof ByteTag){
			return (int) $tag->getValue();
		}else if($tag instanceof ShortTag){
			return (int) $tag->getValue();
		}else if($tag instanceof IntTag){
			return (int) $tag->getValue();
		}else if($tag instanceof LongTag){
			return (int) $tag->getValue();
		}else if($tag instanceof FloatTag){
			return (int) $tag->getValue();
		}else if($tag instanceof DoubleTag){
			return (int) $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getList(string $key, string $listType = "") : array{
		$tag = $this->value[$key];
		if($tag instanceof ListTag){
			if($listType == ""){
				return $tag->getValue();
			}else{
				if($tag->getType() == $listType){
					return $tag->getValue();
				}
			}
		}else{
			return [];
		}
	}

	public function getListTag(string $key) : ListTag{
		$tag = $this->value[$key];
		if($tag instanceof ListTagTag){
			return $tag;
		}else{
			return new ListTag("StirngTag", []);
		}
	}

	public function getLong(string $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof LongTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function asLong(stirng $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof ByteTag){
			return $tag->getValue();
		}else if($tag instanceof ShortTag){
			return $tag->getValue();
		}else if($tag instanceof IntTag){
			return $tag->getValue();
		}else if($tag instanceof LongTag){
			return $tag->getValue();
		}else if($tag instanceof FloatTag){
			return $tag->getValue();
		}else if($tag instanceof DoubleTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getShort(string $key) : float{
		$tag = $this->value[$key];
		if($tag instanceof ShortTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}

	public function getString(string $key) : string{
		$tag = $this->value[$key];
		if($tag instanceof StringTag){
			return $tag->getValue();
		}else{
			return 0;
		}
	}
}