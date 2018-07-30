<?php

namespace edit\regions;

use edit\Vector;
use edit\extent\buffer\ForgetfulExtentBuffer;

class AbstractBufferRegion extends AbstractRegion{

	private $buffer;

	public function __construct(ForgetfulExtentBuffer $buffer){
		$this->buffer = $buffer;
	}

	public function getMinimumPoint() : Vector{
		return $this->buffer->min != null ? $this->buffer->min : new Vector(0, 0, 0);
	}

	public function getMaximumPoint() : Vector{
		return $this->buffer->max != null ? $this->buffer->max : new Vector(0, 0, 0);
	}

	public function expand(array $changes){
	}

	public function contract(array $changes){
	}

	public function contains(Vector $position) : bool{
		return isset($this->buffer->buffer[$position->toBlockVector()->toString()]);
	}

	public function iterator() : array{
		$result = [];

		foreach($this->buffer->buffer as $v => $b){
			$v = ltrim($v, '(');
			$v = rtrim($v, ')');
			$vs = explode(", ", $v);
			$result[] = new Vector($vs[0], $vs[1], $vs[2]);
		}

		return $result;
	}

}