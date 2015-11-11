<?php

namespace Rawr\DataType;
use \Rawr\Core\DispatchTable;

class BaseType
{
  protected $value;

  final public function value()
  {
    return $this->value;
  }

  static function __callStatic($name, $arguments)
  {
    var_dump(["name" => $name, "args" => $arguments]);
  }

  static function defStatic($name, $closure) {
    $table = get_called_class();

    if (!DispatchTable::tableExists($table)) {
      DispatchTable::createTable($table);
    }

    DispatchTable::addToTable($table, $name, rawr_reduce($closure, rawr_callable));
  }
}
