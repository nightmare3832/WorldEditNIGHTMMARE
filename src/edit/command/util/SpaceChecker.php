<?php

namespace edit\command\util;

class SpaceChecker{

    public static function check(array $args) : bool{
        foreach ($args as $value) {
            if ($value === "") {
                return true;
            } 
        }
        return false;
    }
}