<?php

require_once 'src/Core/rawr_core_functions.php';
require_once 'src/DataType/BaseType.php';
require_once 'src/DataType/Bool.php';

use \Rawr\DataType\Bool;

$age = 18;
$canDrive = new Bool($age >= 18);
echo $canDrive->thenElse("Yes, you can!", "No yet!");
