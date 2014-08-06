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
}
