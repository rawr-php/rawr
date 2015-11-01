<?php

define("rawr_object", "object");
define("rawr_boolean", "boolean");
define("rawr_integer", "integer");
define("rawr_double", "double");
define("rawr_resource", "resource");
define("rawr_string", "string");
define("rawr_array", "array");
define("rawr_callable", "callable");

/**
 * Casts a primitive value to an object value.
 * @author Marcelo Camargo
 * @param mixed $op
 * @param mixed string $type
 * @return mixed
 */
function rawr_from_primitive($op, $type)
{
  if (($op_type = rawr_get_primitive_type($op)) === "object") {
    return $op;
  }

  switch ($op_type) {
    case rawr_boolean:
      return new \Rawr\DataType\Bool($op);
    default:
      throw new Exception("[rawr-core] Cannot cast primitive of type"
        . "[{$op_type}] to object-type [{$type}]");
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
    case "object":
      // When we want any object
      if ($type === "object") {
        return $op;
      }

      // When it is possibly a rawr-wrapped value
      if (method_exists($op, "value") && $op instanceof \Rawr\DataType\BaseType) {
        $class_prefix = "Rawr\\DataType\\";
        $unwrapped_value =  $op->value();
        $op_class = get_class($op);

        // Verify if we can cast it to a primitive value with type assertion
        switch ($op_class) {
          case $class_prefix . "Bool" && $type === rawr_boolean:
            return $unwrapped_value;
          /* Add more here */
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
    return "boolean";
  } else if (is_callable($op)) {
    // Ensure this verification occurs before is_object and is_string,
    // because closures are also objects and strings may be callable
    return "callable";
  } else if (is_int($op)) {
    return "integer";
  } else if (is_double($op)) {
    return "double";
  } else if (is_string($op)) {
    return "string";
  } else if (is_array($op)) {
    return "array";
  } else if (is_object($op)) {
    return "object";
  } else if (is_null($op)) {
    return "null";
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
