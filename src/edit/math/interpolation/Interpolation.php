<?php

namespce edit\math\interpolation;

interface Interpolation{

	public function setNodes(array $nodes);

	public function getPosition(float $position) : Vector;

	public function get1stDerivative(float $position) : Vector;

	function arcLength(float $positionA, float $positionB) : float;

	function getSegment(float $position) : int;

}
