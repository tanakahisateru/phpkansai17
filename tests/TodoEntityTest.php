<?php

namespace PhpKansai\TodoManager\Model;


class TodoEntityTest extends \PHPUnit_Framework_TestCase {

    public function testCreate()
    {
        $todo = TodoEntity::create("test");
        $this->assertEquals("test", $todo->content);
    }

    public function testCheck()
    {
        $todo = TodoEntity::create("test");
        $todo->checked = false;
        $todo->check();
        $this->assertTrue($todo->checked);
    }

    public function testUnckeck()
    {
        $todo = TodoEntity::create("test");
        $todo->checked = true;
        $todo->uncheck();
        $this->assertFalse($todo->checked);
    }
}
