<?php

namespace Rawr\DataType;
use \ReflectionFunction;

/**
 * @package Rawr
 * @version 0.9.0
 */
class Action extends BaseType
{
  private $reflection;

  public function __construct($value = NULL)
  {
    rawr_set_default_value($value, function() {});
    if (!is_callable($value)) {
      $type = rawr_get_primitive_type($value);
      throw new \Exception("[rawr-core] [{$type}] is not callable");
    }

    $this->value = &$value;
    $this->reflection = new ReflectionFunction($this->value);
  }

  public function __toString()
  {
    $name = $this->reflection->getName();
    return "#" . ($name === "{closure}")
      ? "<Anonymous Function>"
      : "<$name>";
  }

  public function __invoke()
  {
    $call_result = call_user_func_array($this->value, func_get_args());
    return rawr_from_primitive($call_result, rawr_get_primitive_type($call_result));
  }
}
