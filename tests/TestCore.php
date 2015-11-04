<?php

function import($files)
{
  foreach ($files as $file) {
    require_once "./src/{$file}.php";
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
      rawr_boolean  => true,
      rawr_callable => function() {},
      rawr_integer  => 7,
      rawr_double   => 7.432,
      rawr_string   => "string",
      rawr_array    => [],
      rawr_object   => new stdClass,
      rawr_null     => null
    ];

    $this->values = &$values;

    foreach ($values as $type => $value) {
      $this->assertEquals(rawr_get_primitive_type($value), $type);
    }
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
