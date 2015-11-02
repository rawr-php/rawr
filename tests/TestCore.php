<?php

function import($files)
{
  foreach ($files as $file) {
    require_once './src/' . $file . ".php";
  }
}

import([
  "Core/rawr_core_functions",
  "DataType/BaseType",
  "DataType/Action",
  "DataType/Bool"
]);

class TestCore extends PHPUnit_Framework_TestCase
{
  private $values;

  public function testGetPrimitiveType()
  {
    $values = [
      "bool"     => true,
      "callable" => function() {},
      "integer"  => 7,
      "double"   => 7.432,
      "string"   => "string",
      "array"    => [],
      "object"   => new stdClass,
      "null"     => null
    ];

    $this->values = &$values;

    $this->assertEquals(rawr_get_primitive_type($values["bool"]), rawr_boolean);
    $this->assertEquals(rawr_get_primitive_type($values["callable"]), rawr_callable);
    $this->assertEquals(rawr_get_primitive_type($values["integer"]), rawr_integer);
    $this->assertEquals(rawr_get_primitive_type($values["double"]), rawr_double);
    $this->assertEquals(rawr_get_primitive_type($values["string"]), rawr_string);
    $this->assertEquals(rawr_get_primitive_type($values["array"]), rawr_array);
    $this->assertEquals(rawr_get_primitive_type($values["object"]), rawr_object);
    $this->assertEquals(rawr_get_primitive_type($values["null"]), rawr_null);
  }

  /**
   * @depends testGetPrimitiveType
   */
  public function testNotEqualsGetPrimitiveType()
  {
    $this->assertNotEquals(rawr_get_primitive_type($this->values["bool"]), rawr_boolean);
  }

  public function testSetDefaultValue()
  {
    $nullable = null;

    rawr_set_default_value($nullable, 1);
    $this->assertNotNull($nullable);
    $this->assertEquals(rawr_get_primitive_type($nullable), rawr_integer);
    $this->assertEquals($nullable, 1);
  }

  public function testFromPrimitive()
  {
    $bool = rawr_from_primitive(true, rawr_boolean);
    $action = rawr_from_primitive(function() {
      return true && false;
    }, rawr_callable);

    $this->assertTrue($bool instanceof \Rawr\DataType\Bool);
    $this->assertEquals($bool->value(), true);

    $this->assertTrue($action instanceof \Rawr\DataType\Action);
    $this->assertNotEquals($action(), 1);
    $this->assertTrue($action() instanceof \Rawr\DataType\Bool);
  }
}
