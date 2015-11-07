<?php
/**
 * Copyright (c) 2014 Marcelo Camargo <marcelocamargo@linuxmail.org>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation files
 * (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial of portions the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

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
  private $remaining_size = 0;
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
    $this->remaining_size = $this->length - sizeof($arguments);
  }

  /**
   * Defines the default arguments for the function to apply
   * partialization. This is a variadic method.
   * @author Marcelo Camargo
   * @return void
   */
  public function setArgs()
  {
    $args = [];
    foreach (func_get_args() as $arg) {
      $args[] = $arg;
    }
    $this->args = $args;

    // Update remaining size
    $this->remaining_size = $this->length - sizeof($args);
  }

  /**
   * Exhibition when the action is treated as a string.
   * @author Marcelo Camargo
   * @return string
   */
  public function __toString()
  {
    $name = $this->reflection->getName();
    return "#" . ($name === "{closure}")
      ? "<Anonymous Function>"
      : "<$name>";
  }

  /**
   * Magic method called when we use this object as a function.
   * it applies partialization on the function, where, if the
   * number of received arguments is lesser than the number of
   * the declared function, we'd return a new action pre-filled
   * with the passed arguments.
   * @author Marcelo Camargo
   * @return mixed
   */
  public function __invoke()
  {
    $start_parameters = $this->args;
    $rest_parameters = func_get_args();
    $total_parameters = sizeof($start_parameters) + sizeof($rest_parameters);
    $remaining_size = $this->length - $total_parameters;

    // For internal use only
    $this->remaining_size = &$remaining_size;

    $all_params = array_merge($start_parameters, $rest_parameters);

    if ($remaining_size <= 0) {
      $result = call_user_func_array($this->value, $all_params);
      return rawr_from_primitive($result, rawr_get_primitive_type($result));
    }

    $action = new Action($this->value);

    call_user_func_array([$action, 'setArgs'], $all_params);
    return $action;
  }

  /**
   * Returns the pointer bound to the closure
   * @author Marcelo Camargo
   * @return object
   */
  public function boundPointer()
  {
    return $this->reflection->getClosureThis();
  }

  /**
   * Returns the number of remaining parameters for this action.
   * @author Marcelo Camargo
   * @return Int
   */
  public function getRemainingParameters()
  {
    return /* new Int */($this->remaining_size);
  }

  /**
   * Function composition, where `(f · g)(x) = f(g(x))`.
   * @author Marcelo Camargo
   * @param callable $fn
   * @return Action
   */
  public function ·($fn)
  {
    $left = $this;
    $right = $fn;

    if (!is_callable($right)) {
      throw new \Exception("[rawr-core] Not a function");
    }

    $size_l = $this->getRemainingParameters();
    $size_r = NULL;
    // Get size of the received function
    if ($right instanceof \Rawr\DataType\Action) {
      $size_r = $right->getRemainingParameters();
    } else {
      $size_r = sizeof((new ReflectionFunction($right))->getParameters());
    }

    if ($size_l > $size_r) {
      throw new \Exception("[rawr-core] Left function cannot have greater arity than right");
    }

    if ($size_l > 1) {
      throw new \Exception("[rawr-core] Left function only receives 1 parameter");
    }

    return new Action(function() use ($left, $right) {
      $arguments = func_get_args();
      return call_user_func_array($left, [call_user_func_array($right, $arguments)]);
    });
  }
}

