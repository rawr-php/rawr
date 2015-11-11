<?php

require_once 'src/Core/rawr_core_functions.php';
require_once 'src/Core/DispatchTable.php';

require_once 'src/DataType/BaseType.php';
require_once 'src/DataType/Action.php';
require_once 'src/DataType/Bool.php';
require_once 'src/DataType/Int.php';


use \Rawr\Core\DispatchTable;

use \Rawr\DataType\BaseType;
use \Rawr\DataType\Action;
use \Rawr\DataType\Bool;
use \Rawr\DataType\Int;


Action::defStatic("helloWorld", function($name) {
  echo "Hello {$name}", PHP_EOL;
});

var_dump(DispatchTable::__GET__());
