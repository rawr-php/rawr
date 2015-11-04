<?php

namespace Rawr\DataType;

/**
 * @package Rawr
 * @version 0.9.0
 */
class Int extends BaseType
{
  public function __construct($value = NULL)
  {
    rawr_set_default_value($value, 0);
    if (!is_numeric($value)) {
      $type = rawr_get_primitive_type($value);
      throw new \Exception("[rawr-core] [{$type}] is not a number");
    }

    $this->value = (int) $value;
  }
}
