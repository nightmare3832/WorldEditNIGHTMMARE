<?php

namespace edit\functions\mask;

class Masks{

    private function __construct(){
    }

    public static function alwaysTrue() : Mask{
        return new AlwaysTrue();
    }

    public static function alwaysTrue2D() : Mask2D{
        return new AlwaysTrue();
    }

    public static function negate(Mask $mask) : Mask{
        if($mask instanceof AlwaysTrue){
            return new AlwaysFalse();
        }else if($mask instanceof AlwaysFalse){
            return new AlwaysTrue();
        }
    }
}

    class AlwaysTrue implements Mask, Mask2D{
        public function test($vector) : bool{
            return true;
        }

        public function toMask2D() : Mask2D{
            return $this;
        }
    }

    class AlwaysFalse implements Mask, Mask2D{
        public function test($vector) : bool{
            return false;
        }

        public function toMask2D() : Mask2D{
            return $this;
        }
    }