<?php

namespace edit\functions\operation;

interface Operation{

    function resume(RunContext $run) : ?Operation;

    function cancel();

    function addStatusMessages(array $messages);

}
