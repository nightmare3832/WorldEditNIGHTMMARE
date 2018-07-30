<?php

namespace edit\math\convolution;

class GaussianKernel extends Kernel{

	public function __construct(int $radius, float $sigma){
		parent::__construct($radius * 2 + 1, $radius * 2 + 1, self::createKernel($radius, $sigma));
	}

	private static function createKernel(int $radius, float $sigma) : array{
		$diameter = $radius * 2 + 1;
		$data = [];

		$sigma22 = 2 * $sigma * $sigma;
		$constant = M_PI * $sigma22;
		for($y = -$radius;$y <= $radius;++$y){
			for($x = -$radius;$x <= $radius;++$x){
				$data[($y + $radius) * $diameter + $x + $radius] = (float) (exp(-($x * $x + $y * $y) / $sigma22) / $constant);
			}
		}

		return $data;
	}
}