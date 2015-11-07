<?php

require_once 'src/Core/rawr_core_functions.php';
require_once 'src/DataType/BaseType.php';
require_once 'src/DataType/Action.php';
require_once 'src/DataType/Bool.php';
require_once 'src/DataType/Int.php';

use \Rawr\DataType\Action;
use \Rawr\DataType\Bool;
use \Rawr\DataType\Int;

$add10 = new Action(function($x, Int $y) {
  return $x + $y->value();
}, 10);

$multiply = new Action(function($x, $y) {
  return $x + $y * 3;
}, 5);

$add_and_double = $add10->·($multiply);

var_dump($add_and_double(20)); // => Int(70)
