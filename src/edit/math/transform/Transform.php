<?php

namespace edit\math\transform;

use edit\Vector;

interface Transform{

	function isIdentity() : bool;

	function apply(Vector $input) : Vector;

	function inverse() : Transform;

	function combine(Transform $other) : Transform;

}