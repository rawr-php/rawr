<?php

define("rawr_object", "object");
define("rawr_boolean", "boolean");
define("rawr_integer", "integer");
define("rawr_double", "double");
define("rawr_resource", "resource");
define("rawr_string", "string");
define("rawr_array", "array");
define("rawr_callable", "callable");
define("rawr_null", "null");

/**
 * Casts a primitive value to an object value.
 * @author Marcelo Camargo
 * @param mixed $op
 * @param mixed string $type
 * @return mixed
 */
function rawr_from_primitive($op, $type)
{
  if (($op_type = rawr_get_primitive_type($op)) === rawr_object) {
    return $op;
  }

  if ($op_type !== $type) {
    throw new Exception("[rawr-core] Cannot cast primitive of type [{$op_type}] to object-type [{$type}]");
  }

  switch ($type) {
    case rawr_boolean:
      return new \Rawr\DataType\Bool($op);
    case rawr_callable:
      return new \Rawr\DataType\Action($op);
    case rawr_integer:
      return new \Rawr\DataType\Int($op);
    default:
      throw new \Exception("[rawr-core] Not implemented for [{$type}]");
  }
}

/**
 * Reduces a value to a primitive and applies type checking.
 * @author Marcelo Camargo
 * @param mixed $op
 * @param string $type
 * @return mixed
 */
function rawr_reduce($op, $type)
{
  $op_type = rawr_get_primitive_type($op);

  switch ($op_type) {
    case rawr_object:
    case rawr_callable:
      // When we want any object
      if ($type === rawr_object) {
        return $op;
      }

      // When it is possibly a rawr-wrapped value
      if (method_exists($op, "value") && $op instanceof \Rawr\DataType\BaseType) {
        $class_prefix = "Rawr\\DataType\\";
        $unwrapped_value =  $op->value();
        $op_class = get_class($op);

        // Verify if we can cast it to a primitive value *with type assertion*
        switch ($op_class) {
          case $class_prefix . "Bool" && $type === rawr_boolean:
          case $class_prefix . "Action" && $type === rawr_callable:
            break;
          /* Add more here. Please, remember this. */
          default:
            throw new Exception("[rawr-core] Cannot cast [{$op_class}] to primitive [{$type}]");
        }

        return $unwrapped_value;
      } else {
        throw new Exception("[rawr-core] Trying to extract value from a non-rawr type");
      }
    case "null":
      throw new Exception("[rawr-core] Unable to reduce null value");
    default:
      if ($op_type === $type) {
        return $op;
      } else {
        throw new Exception("[rawr-core] Cannot cast [{$op_type}] to [{$type}]");
      }
  }
}

/**
 * In an optimized way, faster than gettype, returns the type of the primitive
 * value.
 * @author Marcelo Camargo
 * @param mixed $op
 * @return string
 */
function rawr_get_primitive_type($op)
{
  if (is_bool($op)) {
    return rawr_boolean;
  } else if (is_callable($op)) {
    // Ensure this verification occurs before is_object and is_string,
    // because closures are also objects and strings may be callable
    return rawr_callable;
  } else if (is_int($op)) {
    return rawr_integer;
  } else if (is_double($op)) {
    return rawr_double;
  } else if (is_string($op)) {
    return rawr_string;
  } else if (is_array($op)) {
    return rawr_array;
  } else if (is_object($op)) {
    return rawr_object;
  } else if (is_null($op)) {
    return rawr_null;
  }
}

/**
 * Default arguments to hold compatibility with previous PHP versions.
 * @author Marcelo Camargo
 * @param mixed &$var
 * @param mixed $def
 * @return void
 */
function rawr_set_default_value(&$var, $def)
{
  if (is_null($var)) {
    $var = $def;
  }
}
