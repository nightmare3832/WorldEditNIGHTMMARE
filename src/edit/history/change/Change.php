<?php

namespace edit\history\change;

interface Change{

	function undo($session);

	function redo($session);

}