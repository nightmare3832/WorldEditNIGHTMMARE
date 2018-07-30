<?php

namespace edit\math\interpolation;

use edit\Vector;

class KochanekBartelsInterpolation implements Interpolation{

	private $nodes;
	private $coeffA;
	private $coeffB;
	private $coeffC;
	private $coeffD;
	private $scaling;

	public function __construct(){
		$this->setNodes([]);
	}

	public function setNodes(array $nodes){
		$this->nodes = $nodes;
		$this->recalc();
	}

	private function recalc(){
		$nNodes = count($this->nodes);
		$this->coeffA = [];
		$this->coeffB = [];
		$this->coeffC = [];
		$this->coeffD = [];

		if($nNodes == 0) return;

		$nodeB = $this->nodes[0];
		$tensionB = $nodeB->getTension();
		$biasB = $nodeB->getBias();
		$continuityB = $nodeB->getContinuity();
		for($i = 0;$i < $nNodes;++$i){
			$tensionA = $tensionB;
			$biasA = $biasB;
			$continuityA = $continuityB;

			if($i + 1 < $nNodes){
				$nodeB = $this->nodes[$i + 1];
				$tensionB = $nodeB->getTension();
				$biasB = $nodeB->getBias();
				$continuityB = $nodeB->getContinuity();
			}

			$ta = (1-$tensionA)*(1+$biasA)*(1+$continuityA)/2;
			$tb = (1-$tensionA)*(1-$biasA)*(1-$continuityA)/2;
			$tc = (1-$tensionB)*(1+$biasB)*(1-$continuityB)/2;
			$td = (1-$tensionB)*(1-$biasB)*(1+$continuityB)/2;

			$this->coeffA[$i] = $this->linearCombination($i,  -$ta,	   $ta-	 $tb-$tc+2,	   $tb+$tc-$td-2,  $td);
			$this->coeffB[$i] = $this->linearCombination($i, 2*$ta, -2*$ta+2*$tb+$tc-3, -2*$tb-$tc+$td+3, -$td);
			$this->coeffC[$i] = $this->linearCombination($i,  -$ta,	   $ta-	 $tb	 ,	  $tb		 ,	 0);
			$this->coeffD[$i] = $this->retrieve($i);
		}

		$this->scaling = count($this->nodes) - 1;
	}

	private function linearCombination(int $baseIndex, float $f1, float $f2, float $f3, float $f4) : Vector{
		$r1 = $this->retrieve($baseIndex - 1)->multiply($f1);
		$r2 = $this->retrieve($baseIndex	)->multiply($f2);
		$r3 = $this->retrieve($baseIndex + 1)->multiply($f3);
		$r4 = $this->retrieve($baseIndex + 2)->multiply($f4);

		return $r1->add($r2)->add($r3)->add($r4);
	}

	private function retrieve(int $index) : Vector{
		if($index < 0)
			return $this->fastRetrieve(0);

		if($index >= count($this->nodes))
			return $this->fastRetrieve(count($this->nodes)-1);

		return $this->fastRetrieve($index);
	}

	private function fastRetrieve(int $index) : Vector{
		return $this->nodes[$index]->getPosition();
	}

	public function getPosition(float position) : ?Vector{
		if($this->coeffA == null) throw new Exception('Must call setNodes first.', 0);

		if($position > 1) return null;

		$position *= $this->scaling;

		$index = (int) floor($position);
		$remainder = $position - $index;

		$a = $this->coeffA[$index];
		$b = $this->coeffB[$index];
		$c = $this->coeffC[$index];
		$d = $this->coeffD[$index];

		return $a->multiply($remainder)->add(b)->multiply($remainder)->add($c)->multiply($remainder)->add($d);
	}

	public function get1stDerivative(float $position) : ?Vector{
		if($this->coeffA == null) throw new Exception('Must call setNodes first.', 0);

		if($position > 1) return null;

		$position *= $this->scaling;

		$index = (int) floor($position);

		$a = $this->coeffA[$index];
		$b = $this->coeffB[$index];
		$c = $this->coeffC[$index];

		return $a->multiply(1.5*$position - 3.0*$index)->add($b)->multiply(2.0*$position)->add($a->multiply(1.5*$index)->subtract($b)->multiply(2.0*$index))->add($c)->multiply($scaling);
	}

	public function arcLength(double positionA, double positionB) : ?float{
		if($this->coeffA == null) throw new Exception('Must call setNodes first.', 0);

		if($positionA > $positionB)
			return $this->arcLength($positionB, $positionA);

		$positionA *= $this->scaling;
		$positionB *= $this->scaling;

		$indexA = (int) floor($positionA);
		$remainderA = $positionA - $indexA;

		$indexB = (int) floor($positionB);
		$remainderB = $positionB - $indexB;

		return $this->arcLengthRecursive($indexA, $remainderA, $indexB, $remainderB);
	}

	private function arcLengthRecursive(int $indexLeft, float $remainderLeft, int $indexRight, float $remainderRight) : float{
		switch ($indexRight - $indexLeft){
		case 0:
			return $this->arcLengthRecursive($indexLeft, $remainderLeft, $remainderRight);

		case 1:
			// This case is merely a speed-up for a very common case
			return
					$this->arcLengthRecursive($indexLeft, $remainderLeft, 1.0) +
					$this->arcLengthRecursive($indexRight, 0.0, $remainderRight);

		default:
			return
					$this->arcLengthRecursive($indexLeft, $remainderLeft, $indexRight - 1, 1.0) +
					$this->arcLengthRecursive($indexRight, 0.0, $remainderRight);
		}
	}

	private function arcLengthRecursive(int $index, float r$emainderLeft, float $remainderRight) : float{
		$a = $this->coeffA[$index]->multiply(3.0);
		$b = $this->coeffB[$index]->multiply(2.0);
		$c = $this->coeffC[$index];

		$nPoints = 8;

		$accum = $a->multiply($remainderLeft)->add($b)->multiply($remainderLeft)->add($c)->length() / 2.0;
		for($i = 1;$i < $nPoints-1;++$i) {
			$t = ((float) $i) / $nPoints;
			$t = ($remainderRight-$remainderLeft)*$t + $remainderLeft;
			$accum += $a.multiply($t)->add($b)->multiply($t)->add($c)->length();
		}

		$accum += $a->multiply($remainderRight)->add($b)->multiply($remainderRight)->add($c)->length() / 2.0;
		return $accum * ($remainderRight - $remainderLeft) / $nPoints;
	}

	public function getSegment(float $position) : ?int{
		if($this->coeffA == null) throw new Exception('Must call setNodes first.', 0);

		if($position > 1) return PHP_INT_MAX;

		$position *= $this->scaling;

		return (int) floor($position);
	}
}