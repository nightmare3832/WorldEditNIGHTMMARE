<?php

namespace edit\regions;

use edit\Vector;

interface Region{

    public function getMinimumPoint() : Vector;

    public function getMaximumPoint() : Vector;

    public function getCenter() : Vector;

    public function getWidth() : int;

    public function getHeight() : int;

    public function getLength() : int;
}