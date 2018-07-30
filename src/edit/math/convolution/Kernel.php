<?php

namespace edit\math\convolution;

class Kernel{

	private $width;
	private $height;
	private $xOrigin;
	private $yOrigin;
	private $data;

	public function __construct(int $width, int $height, array $data){
		$this->width = $width;
		$this->height = $height;
		$this->xOrigin = ($width - 1) >> 1;
		$this->yOrigin = ($height - 1) >> 1;
		$len = $width * $height;
		if(count($data) < $len){
			echo "error";
		}
		for($i = 0;$i < count($data);$i++){
			$this->data[$i] = $data[$i];
		}
	}

	public function getXOrigin() : int{
		return $this->xOrigin;
	}

	public function getYOrigin() : int{
		return $this->yOrigin;
	}

	public function getWidth() : int{
		return $this->width;
	}

	public function getHeight() : int{
		return $this->height;
	}

	public function getKernelData(array $data = []) : array{
		for($i = 0;$i < count($data);$i++){
			$this->data[$i] = $data[$i];
		}
		return $this->data;
	}
}