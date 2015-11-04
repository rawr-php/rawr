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
  "DataType/Bool",
  "DataType/Int"
]);

class TestAction extends PHPUnit_Framework_TestCase
{
  public function testIdAction()
  {
    $id = new \Rawr\DataType\Action(function($x) { return $x; });
    $true = $id(true);

    $this->assertTrue($true instanceof \Rawr\DataType\Bool);
    $this->assertEquals(true, $true->value());
  }

  public function testPreArgs()
  {
    $and = new \Rawr\DataType\Action(function($x, $y) {
      return $x && $y;
    });

    $and->setArgs(true);
    $result = $and(false);

    $this->assertFalse($result->value());
    $this->assertTrue($result instanceof \Rawr\DataType\Bool);
  }

  public function testName()
  {
    $anonymous = new \Rawr\DataType\Action(function() {});
    $phpinfo = new \Rawr\DataType\Action('phpinfo');

    $this->assertEquals("<Anonymous Function>", (string) $anonymous);
    $this->assertEquals("<Anonymous Function>", (string) $phpinfo);
  }

  /**
   * @expectedException Exception
   */
  public function testNotFoundFunction()
  {
    new \Rawr\DataType\Action('some_weird_func_call');
  }

  public function testPartialFunction()
  {
    function add($a, $b, $c)
    {
      return $a + $b + $c;
    }

    $add = new \Rawr\DataType\Action('add');

    $a = $add;
    $b = $add(10);
    $c = $add(10, 20);
    $d = $add(10, 20, 30);

    $this->assertEquals(60, $a(10, 20, 30)->value());
    $this->assertEquals(60, $b(20, 30)->value());
    $this->assertEquals(60, $c(30)->value());
    $this->assertEquals(60, $d->value());

    $this->assertTrue($d instanceof \Rawr\DataType\Int);
  }
}
