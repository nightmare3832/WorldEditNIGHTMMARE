<?php

namespace edit\math;

class MathUtils {

	//const SAFE_MIN = 0x1.0p-1022;

	private function __construct(){
	}

	public static function divisorMod(int $a, int $n) : int{
		return (int) ($a - $n * floor(floor($a) / $n));
	}

	public static function dCos(float $degrees) : float{
		$dInt = (int) $degrees;
		if($degrees == $dInt && $dInt % 90 == 0){
			$dInt %= 360;
			if($dInt < 0){
				$dInt += 360;
			}
			switch($dInt){
			case 0:
				return 1.0;
			case 90:
				return 0.0;
			case 180:
				return -1.0;
			case 270:
				return 0.0;
			}
		}
		return cos(deg2rad($degrees));
	}

	public static function dSin(float $degrees) : float{
		$dInt = (int) $degrees;
		if($degrees == $dInt && $dInt % 90 == 0){
			$dInt %= 360;
			if($dInt < 0){
				$dInt += 360;
			}
			switch($dInt){
			case 0:
				return 0.0;
			case 90:
				return 1.0;
			case 180:
				return 0.0;
			case 270:
				return -1.0;
			}
		}
		return sin(deg2rad($degrees));
	}

	public static function roundHalfUp(float $value) : float{
		return signum($value) * round(abs($value));
	}
}