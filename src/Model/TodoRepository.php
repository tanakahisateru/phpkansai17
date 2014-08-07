<?php

namespace PhpKansai\TodoManager\Model;


class TodoRepository
{
    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * @param \Doctrine\DBAL\Connection $conn
     */
    public function setConnection($conn)
    {
        $this->connection = $conn;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param TodoEntity $todo
     */
    public function append($todo)
    {
        $this->connection->insert(
            'todo',
            [
                'content' => $todo->content,
                'checked' => intval($todo->checked),
            ]
        );
        $todo->id = $this->getConnection()->lastInsertId();
    }

    /**
     * @return TodoEntity[]
     */
    public function findAll()
    {
        $all = $this->connection->fetchAll(
            "SELECT * FROM todo ORDER BY id"
        );

        return array_map([$this, 'row2entity'], $all);
    }

    /**
     * @param int $id
     * @return TodoEntity
     */
    public function findById($id)
    {
        $row = $this->connection->fetchAssoc(
            "SELECT * FROM todo WHERE id = :id",
            [
                ':id' => $id,
            ]
        );
        if (empty($row)) {
            return null;
        }
        $todo = $this->row2entity($row);
        return $todo;
    }

    /**
     * @param TodoEntity $todo
     */
    public function syncCheckStatus($todo)
    {
        $this->connection->update(
            'todo',
            [
                'checked' => intval($todo->checked)
            ],
            [
                'id' => $todo->id
            ]
        );
    }

    /**
     * @param TodoEntity $todo
     */
    public function remove($todo)
    {
        $this->connection->delete(
            'todo',
            [
                'id' => $todo->id
            ]
        );
    }

    /**
     * @param array $row
     * @return TodoEntity
     */
    private function row2entity($row)
    {
        $todo = new TodoEntity();
        $todo->id = intval($row['id']);
        $todo->content = $row['content'];
        $todo->checked = boolval($row['checked']);
        return $todo;
    }
}
