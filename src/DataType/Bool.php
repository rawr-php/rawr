<?php

namespace Rawr\DataType;

/**
 * @package Rawr
 * @version 0.9.0
 */
class Bool extends BaseType
{
  public function __construct($value = NULL)
  {
    rawr_set_default_value($value, false);
    $this->value = (bool) $value;
  }

  private function getHomegenousPair(&$a)
  {
    return [$this->value, rawr_reduce($a, rawr_boolean)];
  }

  public function _and($b)
  {
    list ($left, $right) = $this->getHomegenousPair($b);
    return rawr_from_primitive($left && $right, rawr_boolean);
  }

  public function _or($b)
  {
    list ($left, $right) = $this->getHomegenousPair($b);
    return rawr_from_primitive($left || $right, rawr_boolean);
  }

  public function diff($b)
  {
    list ($left, $right) = $this->getHomegenousPair($b);
    return rawr_from_primitive($left !== $right, rawr_boolean);
  }

  public function eq($b)
  {
    list ($left, $right) = $this->getHomegenousPair($b);
    return rawr_from_primitive($left === $right, rawr_boolean);
  }

  public function ifTrue($f)
  {
    $left = $this->value;
    $right = NULL;

    if ($left) {
      // Avoid memory overflow. Reduce only when necessary
      $right = rawr_reduce($f, rawr_callable);
      call_user_func($right);
    }

    return $this;
  }

  public function ifFalse($f)
  {
    $left = $this->value;
    $right = NULL;

    if (!left) {
      $right = rawr_reduce($f, rawr_callable);
      call_user_func($right);
    }

    return $this;
  }

  public function negate()
  {
    return rawr_from_primitive(!$this->value);
  }

  public function thenElse($then, $else)
  {
    return $this->value ? $then : $else;
  }
}
