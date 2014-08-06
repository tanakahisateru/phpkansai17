<?php

namespace PhpKansai\TodoManager\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TodoController
{
    /**
     * @param Application $app
     */
    function __construct($app)
    {
    }

    /**
     * GET:"index"
     */
    public function indexAction()
    {
        return "index page";
    }

    /**
     * POST:"todo"
     */
    public function appendAction(Request $request)
    {
        return "";
    }

    /**
     * PATCH:"todo/{id}/check"
     */
    public function checkAjaxAction($id, Request $request)
    {
        return "";
    }

    /**
     * DELETE:"todo/{id}"
     */
    public function removeAction($id)
    {
        return "";
    }
}