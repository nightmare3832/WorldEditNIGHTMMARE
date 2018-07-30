<?php

namespace edit\math\transform;

use edit\Vector;
use edit\math\MathUtils;

class AffineTransform implements Transform{

	private $m00;
	private $m01;
	private $m02;
	private $m03;

	private $m10;
	private $m11;
	private $m12;
	private $m13;

	private $m20;
	private $m21;
	private $m22;
	private $m23;

	public function __construct($xx = null, $yx = null, $zx = null, $tx = null, $xy = null, $yy = null, $zy = null, $ty = null, $xz = null, $yz = null, $zz = null, $tz = null){
		if(is_numeric($xx)){
			$this->m00 = $xx;
			$this->m01 = $yx;
			$this->m02 = $zx;
			$this->m03 = $tx;
			$this->m10 = $xy;
			$this->m11 = $yy;
			$this->m12 = $zy;
			$this->m13 = $ty;
			$this->m20 = $xz;
			$this->m21 = $yz;
			$this->m22 = $zz;
			$this->m23 = $tz;
		}else if($xx != null){
			if(count($xx) == 9){
				$this->m00 = $xx[0];
				$this->m01 = $xx[1];
				$this->m02 = $xx[2];
				$this->m10 = $xx[3];
				$this->m11 = $xx[4];
				$this->m12 = $xx[5];
				$this->m20 = $xx[6];
				$this->m21 = $xx[7];
				$this->m22 = $xx[8];
			}else if(count($xx) == 12){
				$this->m00 = $xx[0];
				$this->m01 = $xx[1];
				$this->m02 = $xx[2];
				$this->m03 = $xx[3];
				$this->m10 = $xx[4];
				$this->m11 = $xx[5];
				$this->m12 = $xx[6];
				$this->m13 = $xx[7];
				$this->m20 = $xx[8];
				$this->m21 = $xx[9];
				$this->m22 = $xx[10];
				$this->m23 = $xx[11];
			}
		}else{
			$this->m00 = $this->m11 = $this->m22 = 1;
			$this->m01 = $this->m02 = $this->m03 = 0;
			$this->m10 = $this->m12 = $this->m13 = 0;
			$this->m20 = $this->m21 = $this->m23 = 0;
		}
	}

	public function isIdentity() : bool{
		if($this->m00 != 1)
			return false;
		if($this->m11 != 1)
			return false;
		if($this->m22 != 1)
			return false;
		if($this->m01 != 0)
			return false;
		if($this->m02 != 0)
			return false;
		if($this->m03 != 0)
			return false;
		if($this->m10 != 0)
			return false;
		if($this->m12 != 0)
			return false;
		if($this->m13 != 0)
			return false;
		if($this->m20 != 0)
			return false;
		if($this->m21 != 0)
			return false;
		if($this->m23 != 0)
			return false;
		return true;
	}

	public function coefficients() : array{
		return [$this->m00, $this->m01, $this->m02, $this->m03, $this->m10, $this->m11, $this->m12, $this->m13, $this->m20, $this->m21, $this->m22, $this->m23];
	}

	private function determinant() : float{
		return $this->m00 * ($this->m11 * $this->m22 - $this->m12 * $this->m21) - $this->m01 * ($this->m10 * $this->m22 - $this->m20 * $this->m12)
				+ $this->m02 * ($this->m10 * $this->m21 - $this->m20 * $this->m11);
	}

	public function inverse() : Transform{
		$det = $this->determinant();
		return new AffineTransform(
				($this->m11 * $this->m22 - $this->m21 * $this->m12) / $det,
				($this->m21 * $this->m02 - $this->m01 * $this->m22) / $det,
				($this->m01 * $this->m12 - $this->m11 * $this->m02) / $det,
				($this->m01 * ($this->m22 * $this->m13 - $this->m12 * $this->m23) + $this->m02 * ($this->m11 * $this->m23 - $this->m21 * $this->m13)
						- $this->m03 * ($this->m11 * $this->m22 - $this->m21 * $this->m12)) / $det,
				($this->m20 * $this->m12 - $this->m10 * $this->m22) / $det,
				($this->m00 * $this->m22 - $this->m20 * $this->m02) / $det,
				($this->m10 * $this->m02 - $this->m00 * $this->m12) / $det,
				($this->m00 * ($this->m12 * $this->m23 - $this->m22 * $this->m13) - $this->m02 * ($this->m10 * $this->m23 - $this->m20 * $this->m13)
						+ $this->m03 * ($this->m10 * $this->m22 - $this->m20 * $this->m12)) / $det,
				($this->m10 * $this->m21 - $this->m20 * $this->m11) / $det,
				($this->m20 * $this->m01 - $this->m00 * $this->m21) / $det,
				($this->m00 * $this->m11 - $this->m10 * $this->m01) / $det,
				($this->m00 * ($this->m21 * $this->m13 - $this->m11 * $this->m23) + $this->m01 * ($this->m10 * $this->m23 - $this->m20 * $this->m13)
						- $this->m03 * ($this->m10 * $this->m21 - $this->m20 * $this->m11)) / $det);
	}

	public function concatenate(AffineTransform $that) : AffineTransform{
		$n00 = $this->m00 * $that->m00 + $this->m01 * $that->m10 + $this->m02 * $that->m20;
		$n01 = $this->m00 * $that->m01 + $this->m01 * $that->m11 + $this->m02 * $that->m21;
		$n02 = $this->m00 * $that->m02 + $this->m01 * $that->m12 + $this->m02 * $that->m22;
		$n03 = $this->m00 * $that->m03 + $this->m01 * $that->m13 + $this->m02 * $that->m23 + $this->m03;
		$n10 = $this->m10 * $that->m00 + $this->m11 * $that->m10 + $this->m12 * $that->m20;
		$n11 = $this->m10 * $that->m01 + $this->m11 * $that->m11 + $this->m12 * $that->m21;
		$n12 = $this->m10 * $that->m02 + $this->m11 * $that->m12 + $this->m12 * $that->m22;
		$n13 = $this->m10 * $that->m03 + $this->m11 * $that->m13 + $this->m12 * $that->m23 + $this->m13;
		$n20 = $this->m20 * $that->m00 + $this->m21 * $that->m10 + $this->m22 * $that->m20;
		$n21 = $this->m20 * $that->m01 + $this->m21 * $that->m11 + $this->m22 * $that->m21;
		$n22 = $this->m20 * $that->m02 + $this->m21 * $that->m12 + $this->m22 * $that->m22;
		$n23 = $this->m20 * $that->m03 + $this->m21 * $that->m13 + $this->m22 * $that->m23 + $this->m23;
		return new AffineTransform(
				$n00, $n01, $n02, $n03,
				$n10, $n11, $n12, $n13,
				$n20, $n21, $n22, $n23);
	}

	public function preConcatenate(AffineTransform $that) : AffineTransform{
		$n00 = $that->m00 * $this->m00 + $that->m01 * $this->m10 + $that->m02 * $this->m20;
		$n01 = $that->m00 * $this->m01 + $that->m01 * $this->m11 + $that->m02 * $this->m21;
		$n02 = $that->m00 * $this->m02 + $that->m01 * $this->m12 + $that->m02 * $this->m22;
		$n03 = $that->m00 * $this->m03 + $that->m01 * $this->m13 + $that->m02 * $this->m23 + $that->m03;
		$n10 = $that->m10 * $this->m00 + $that->m11 * $this->m10 + $that->m12 * $this->m20;
		$n11 = $that->m10 * $this->m01 + $that->m11 * $this->m11 + $that->m12 * $this->m21;
		$n12 = $that->m10 * $this->m02 + $that->m11 * $this->m12 + $that->m12 * $this->m22;
		$n13 = $that->m10 * $this->m03 + $that->m11 * $this->m13 + $that->m12 * $this->m23 + $that->m13;
		$n20 = $that->m20 * $this->m00 + $that->m21 * $this->m10 + $that->m22 * $this->m20;
		$n21 = $that->m20 * $this->m01 + $that->m21 * $this->m11 + $that->m22 * $this->m21;
		$n22 = $that->m20 * $this->m02 + $that->m21 * $this->m12 + $that->m22 * $this->m22;
		$n23 = $that->m20 * $this->m03 + $that->m21 * $this->m13 + $that->m22 * $this->m23 + $that->m23;
		return new AffineTransform(
				$n00, $n01, $n02, $n03,
				$n10, $n11, $n12, $n13,
				$n20, $n21, $n22, $n23);
	}

	public function translate($x, $y = null, $z = null) : AffineTransform{
		if($y == null){
			$y = $x->getY();
			$z = $x->getZ();
			$x = $x->getX();
		}
		return $this->concatenate(new AffineTransform(1, 0, 0, $x, 0, 1, 0, $y, 0, 0, 1, $z));
	}

	public function rotateX(float $theta) : AffineTransform{
		$cot = MathUtils::dCos($theta);
		$sit = MathUtils::dSin($theta);
		return $this->concatenate(
				new AffineTransform(
						1, 0, 0, 0,
						0, $cot, -$sit, 0,
						0, $sit, $cot, 0));
	}

	public function rotateY(float $theta) : AffineTransform{
		$cot = MathUtils::dCos($theta);
		$sit = MathUtils::dSin($theta);
		return $this->concatenate(
				new AffineTransform(
						$cot, 0, $sit, 0,
						0, 1, 0, 0,
						-$sit, 0, $cot, 0));
	}

	public function rotateZ(float $theta) : AffineTransform{
		$cot = MathUtils::dCos($theta);
		$sit = MathUtils::dSin($theta);
		return $this->concatenate(
				new AffineTransform(
						$cot, -$sit, 0, 0,
						$sit, $cot, 0, 0,
						0, 0, 1, 0));
	}

	public function scale($sx, $sy = null, $sz = null) : AffineTransform{
		if($sx instanceof Vector){
			$sy = $sx->getY();
			$sz = $sx->getZ();
			$sx = $sx->getX();
		}
		if($sy == null){
			$sy = $sx;
			$sz = $sx;
		}
		return $this->concatenate(new AffineTransform($sx, 0, 0, 0, 0, $sy, 0, 0, 0, 0, $sz, 0));
	}

	public function apply(Vector $vector) : Vector{
		return new Vector(
				$vector->getX() * $this->m00 + $vector->getY() * $this->m01 + $vector->getZ() * $this->m02 + $this->m03,
				$vector->getX() * $this->m10 + $vector->getY() * $this->m11 + $vector->getZ() * $this->m12 + $this->m13,
				$vector->getX() * $this->m20 + $vector->getY() * $this->m21 + $vector->getZ() * $this->m22 + $this->m23);
	}

	public function combine(Transform $other) : Transform{
		if($other instanceof AffineTransform){
			return $this->concatenate($other);
		}else{
			return new CombinedTransform([$this, $other]);
		}
	}


}