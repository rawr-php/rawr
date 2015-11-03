<?php

require_once 'src/Core/rawr_core_functions.php';
require_once 'src/DataType/BaseType.php';
require_once 'src/DataType/Action.php';
require_once 'src/DataType/Bool.php';

use \Rawr\DataType\Action;
use \Rawr\DataType\Bool;



$boolean_id = new Action(function($x, $y) {
  return $x && $y ;
}, true);

var_dump($boolean_id(true, false));
