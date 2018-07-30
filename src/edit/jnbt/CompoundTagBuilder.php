<?php

namespace edit\jnbt;

class CompoundTagBuilder{

	private $entries;

	public function __construct(array $value = []){
		$this->entries = $value;
	}

	public function put(string $key, Tag $value) : CompoundTagBuilder{
		$this->entries[$key] = $value;
		return $this;
	}

	public function putByteArray(string $key, array $value) : CompoundTagBuilder{
		$this->entries[$key] = new ByteArrayTag($value);
		return $this;
	}

	public function putByte(string $key, int $value) : CompoundTagBuilder{
		$this->entries[$key] = new ByteTag($value);
		return $this;
	}

	public function putDouble(string $key, float $value) : CompoundTagBuilder{
		$this->entries[$key] = new DoubleTag($value);
		return $this;
	}

	public function putFloat(string $key, float $value) : CompoundTagBuilder{
		$this->entries[$key] = new FloatTag($value);
		return $this;
	}

	public function putIntArray(string $key, array $value) : CompoundTagBuilder{
		$this->entries[$key] = new IntArrayTag($value);
		return $this;
	}

	public function putInt(string $key, int $value) : CompoundTagBuilder{
		$this->entries[$key] = new IntTag($value);
		return $this;
	}

	public function putLong(string $key, float $value) : CompoundTagBuilder{
		$this->entries[$key] = new LongTag($value);
		return $this;
	}

	public function putShort(string $key, float $value) : CompoundTagBuilder{
		$this->entries[$key] = new ShortTag($value);
		return $this;
	}

	public function putString(string $key, string $value) : CompoundTagBuilder{
		$this->entries[$key] = new StringTag($value);
		return $this;
	}

	public function putAll(string $key, array $values) : CompoundTagBuilder{
		foreach($values as $value){
			$this->entries[$key] =$value;
		}
		return $this;
	}

	public function build() : CompoundTag{
		return new CompoundTag($this->entries);
	}

	public static function create() : CompoundTagBuilder{
		return new CompoundTagBuilder();
	}
