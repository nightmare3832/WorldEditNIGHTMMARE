<?php

namespace edit\util;

class DirectionFlag{

	const CARDINAL = 0x1;
	const ORDINAL = 0x2;
	const SECONDARY_ORDINAL = 0x4;
	const UPRIGHT = 0x8;

	const ALL = self::CARDINAL | self::ORDINAL | self::SECONDARY_ORDINAL | self::UPRIGHT;
}