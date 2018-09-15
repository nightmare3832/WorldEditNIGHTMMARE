<?php

namespace edit\command\util;

class HelpChecker{

    public static function check(array $args) : bool{
        if(isset($args[0])) {
            if($args[0] == "help") {
                return true;
            }
        }
        return false;
    }
}