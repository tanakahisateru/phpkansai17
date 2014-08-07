<?php

namespace PhpKansai\TodoManager\Model;


class TodoEntity
{
    /** @var int */
    public $id;

    /** @var string */
    public $content;

    /** @var bool */
    public $checked;

    /**
     *
     */
    function __construct()
    {
        $this->id = null;
        $this->content = "";
        $this->checked = false;
    }

    /**
     * @param string $content
     * @return TodoEntity
     */
    public static function create($content)
    {
        $todo = new self();
        $todo->content = $content;
        return $todo;
    }

    /**
     *
     */
    public function check()
    {
        $this->checked = true;
    }

    /**
     *
     */
    public function uncheck()
    {
        $this->checked = false;
    }
}
