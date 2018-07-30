<?php

namespace edit\math\convolution;

class HeightMapFilter{

	private $kernel;

	public function __construct(Kernel $kernel){
		$this->kernel = $kernel;
	}

	public function getKernel() : Kernel{
		return $this->kernel;
	}

	public function setKernel(Kernel $kernel){
		$this->kernel = $kernel;
	}

	public function filter(array $inData, int $width, int $height){
		$index = 0;
		$matrix = $this->kernel->getKernelData();
		$outData = [];

		$kh = $this->kernel->getHeight();
		$kw = $this->kernel->getWidth();
		$kox = $this->kernel->getXOrigin();
		$koy = $this->kernel->getYOrigin();

		for($y = 0;$y < $height;++$y){
			for($x = 0;$x < $width;++$x){
				$z = 0;
				for($ky = 0;$ky < $kh;++$ky){
					$offsetY = $y + $ky - $koy;

					if($offsetY < 0 || $offsetY >= $height){
						$offsetY = $y;
					}

					$offsetY *= $width;

					$matrixOffset = $ky * $kw;
					for($kx = 0;$kx < $kw;++$kx){
						$f = $matrix[$matrixOffset + $kx];
						if($f == 0) continue;

						$offsetX = $x + $kx - $kox;

						if($offsetX < 0 || $offsetX >= $width){
							$offsetX = $x;
						}

						$z += $f * $inData[$offsetY + $offsetX];
					}
				}
				$outData[$index++] = (int) ($z + 0.5);
			}
		}
		return $outData;
	}
}