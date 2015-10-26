<?php

require_once 'src/Core/rawr_core_functions.php';
require_once 'src/DataType/BaseType.php';
require_once 'src/DataType/Bool.php';

var_dump(rawr_reduce(new \Rawr\DataType\Bool(true), rawr_boolean));
