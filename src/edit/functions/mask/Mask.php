<?php

namespace edit\functions\mask;

use edit\Vector;

interface Mask{

	function test($vector) : bool;

	function toMask2D() : ?Mask2D;
}