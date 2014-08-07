<?php

namespace PhpKansai\TodoManager\Model;


use PhpKansai\TodoManager\Helper\TestDatabaseHelper;

class TodoRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var TodoRepository */
    protected $repository;

    public function testAppend()
    {
        $todo = TodoEntity::create("test");
        $this->repository->append($todo);

        $this->assertNotNull($todo->id);

        $num = $this->repository->getConnection()->fetchColumn(
            "SELECT COUNT(*) FROM todo"
        );
        $this->assertEquals(1, $num);
    }

    public function testFindAll()
    {
        $this->repository->append(TodoEntity::create("test1"));
        $this->repository->append(TodoEntity::create("test2"));
        $this->repository->append(TodoEntity::create("test3"));

        $all = $this->repository->findAll();

        $this->assertCount(3, $all);
        $this->assertEquals("test1", $all[0]->content);
        $this->assertEquals("test2", $all[1]->content);
        $this->assertEquals("test3", $all[2]->content);
    }

    public function testFindById()
    {
        $todo = TodoEntity::create("test");
        $this->repository->append($todo);

        $notfound = $this->repository->findById(1000);
        $this->assertNull($notfound);

        $restored = $this->repository->findById($todo->id);
        $this->assertNotNull($restored);
        $this->assertEquals("test", $restored->content);
    }

    public function testSyncCheckStatus()
    {
        $todo = TodoEntity::create("test");
        $this->repository->append($todo);

        $todo->check();
        $this->repository->syncCheckStatus($todo);

        $restored = $this->repository->findById($todo->id);
        $this->assertTrue($restored->checked);

        $todo->uncheck();
        $this->repository->syncCheckStatus($todo);

        $restored = $this->repository->findById($todo->id);
        $this->assertFalse($restored->checked);
    }

    public function testRemove()
    {
        $todo = TodoEntity::create("test");
        $this->repository->append($todo);
        $id = $todo->id;

        $this->repository->remove($todo);

        $restored = $this->repository->findById($id);
        $this->assertNull($restored);
    }

    protected function setUp()
    {
        $this->repository = new TodoRepository();
        $this->repository->setConnection(TestDatabaseHelper::createConnection());
    }

    protected function tearDown()
    {
        TestDatabaseHelper::clean();
    }
}
