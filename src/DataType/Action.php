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
  private $args = [];
  public $length = 0;

  public function __construct($value = NULL)
  {
    rawr_set_default_value($value, function() {});
    if (!is_callable($value)) {
      $type = rawr_get_primitive_type($value);
      throw new \Exception("[rawr-core] [{$type}] is not callable");
    }

    $arguments = func_get_args();
    array_shift($arguments);

    $this->value = $value;
    $this->reflection = new ReflectionFunction($this->value);
    $this->length = count($this->reflection->getParameters());
    $this->args = $arguments;
  }

  public function setArgs()
  {
    $args = [];
    foreach (func_get_args() as $arg) {
      $args[] = $arg;
    }
    $this->args = $args;
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
    $start_parameters = $this->args;
    $rest_parameters = func_get_args();
    $total_parameters = count($start_parameters) + count($rest_parameters);
    $remaining_size = $this->length - $total_parameters;

    $all_params = array_merge($start_parameters, $rest_parameters);

    if ($remaining_size <= 0) {
      $result = call_user_func_array($this->value, $all_params);
      return rawr_from_primitive($result, rawr_get_primitive_type($result));
    }

    $action = new Action($this->value);

    call_user_func_array([$action, 'setArgs'], $all_params);
    return $action;
  }
}

