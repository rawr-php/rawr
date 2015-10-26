<?php

namespace Rawr\DataType;

/**
 * @package Rawr
 * @version 0.9.0
 */
class Bool extends BaseType
{
  public function __construct($value)
  {
    $this->value = (bool) $value;
  }

  public function _and(&$b)
  {
    $left = &$this->value;
    $right = rawr_reduce($b, rawr_boolean);
  }
}
