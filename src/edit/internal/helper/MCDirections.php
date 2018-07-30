<?php

namespace edit\internal\helper;

use edit\util\Direction;

class MCDirections {

	private function __construct(){
	}

	public static function fromHanging(int $i) : Direction{
		switch($i){
			case 0:
				return Direction::$SOUTH;
			case 1:
				return Direction::$WEST;
			case 2:
				return Direction::$NORTH;
			case 3:
				return Direction::$EAST;
			default:
				return Direction::$NORTH;
		}
	}

	public static function toHanging(Direction $direction) : int{
		switch($direction->toVector()->x){
			case 0:
				switch($direction->toVector()->z){
					case -1:
						return 2;//NORTH
					case 1:
						return 0;//SOUTH
				}
			case 1:
				switch($direction->toVector()->z){
					case 0:
						return 3;//EAST
				}
			case -1:
				switch($direction->toVector()->z){
					case 0:
						return 1;//WEST
				}
			default:
				return 0;
		}
	}

	public static function fromLegacyHanging(int $i) : int{
		switch($i){
			case 0: return 2;
			case 1: return 1;
			case 2: return 0;
			default: return 3;
		}
	}

	public static function toLegacyHanging(int $i) : int{
		switch($i){
			case 0: return 2;
			case 1: return 1;
			case 2: return 0;
			default: return 3;
		}
	}

	public static function fromRotation(int $i) : Direction{
		switch($i){
			case 0:
				return Direction::$SOUTH;
			case 1:
				return Direction::$SOUTH_SOUTHWEST;
			case 2:
				return Direction::$SOUTHWEST;
			case 3:
				return Direction::$WEST_SOUTHWEST;
			case 4:
				return Direction::$WEST;
			case 5:
				return Direction::$WEST_NORTHWEST;
			case 6:
				return Direction::$NORTHWEST;
			case 7:
				return Direction::$NORTH_NORTHWEST;
			case 8:
				return Direction::$NORTH;
			case 9:
				return Direction::$NORTH_NORTHEAST;
			case 10:
				return Direction::$NORTHEAST;
			case 11:
				return Direction::$EAST_NORTHEAST;
			case 12:
				return Direction::$EAST;
			case 13:
				return Direction::$EAST_SOUTHEAST;
			case 14:
				return Direction::$SOUTHEAST;
			case 15:
				return Direction::$SOUTH_SOUTHEAST;
			default:
				return Direction::$NORTH;
		}
	}

	public static int toRotation(Direction direction) {
		switch($direction->toVector()->x){
			case 0:
				switch($direction->toVector()->z){
					case -1:
						return 8;//NORTH
					case 1:
						return 0;//SOUTH
					case 0:
						switch($direction->toVector()->y){
							case 1:
								return 0;//UP
							case -1;
								return 0;//DOWN
						}
				}
			case 1:
				switch($direction->toVector()->z){
					case -1:
						return 10;//NORTHEAST
					case 1:
						return 14;//SOUTHEAST
					case 0:
						return 12;//EAST
				}
			case -1:
				switch($direction->toVector()->z){
					case -1:
						return 6;//NORTHWEST
					case 1:
						return 2;//SOUTHWEST
					case 0:
						return 4;//WEST
				}
			case -cos(M_PI / 8):
				switch($direction->toVector()->z){
					case -sin(M_PI / 8):
						return 5;//WEST_NORTHWEST
					case sin(M_PI / 8):
						return 3;//WEST_SOUTHWEST
				}
			case cos(M_PI / 8):
				switch($direction->toVector()->z){
					case -sin(M_PI / 8):
						return 11;//EAST_NORTHEAST
					case sin(M_PI / 8):
						return 13;//EAST_SOUTHEAST
				}
			case -sin(M_PI / 8):
				switch($direction->toVector()->z){
					case -cos(M_PI / 8):
						return 7;//NORTH_NORTHWEST
					case cos(M_PI / 8):
						return 1;//SOUTH_SOUTHWEST
				}
			case sin(M_PI / 8):
				switch($direction->toVector()->z){
					case -cos(M_PI / 8):
						return 9;//NORTH_NORTHEAST
					case cos(M_PI / 8):
						return 15;//SOUTH_SOUTHEAST
				}
			default:
				return 0;
		}
	}

}