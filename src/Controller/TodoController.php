<?php

namespace PhpKansai\TodoManager\Controller;


use PhpKansai\TodoManager\Model\TodoEntity;
use PhpKansai\TodoManager\Model\TodoRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class TodoController
{
    /** @var \Silex\Application */
    protected $app;

    /** @var \Twig_Environment */
    protected $twig;

    /** @var TodoRepository */
    protected $repository;

    /**
     * @param Application $app
     */
    function __construct($app)
    {
        $this->app = $app;
        $this->twig = $app['twig'];
        $this->repository = $app['todo.repository'];
    }

    /**
     * GET:"index"
     */
    public function indexAction()
    {
        return $this->twig->render('index.html.twig', [
            'entities' => $this->repository->findAll(),
        ]);
    }

    /**
     * POST:"todo"
     */
    public function appendAction(Request $request)
    {
        $content = $request->request->get('content');
        if (!is_null($content)) {
            $this->repository->append(TodoEntity::create($content));
            return $this->app->redirect('/');
        } else {
            return $this->twig->render('index.html.twig', [
                'error' => "正しく入力して下さい",
                'entities' => $this->repository->findAll(),
            ]);
       }
    }

    /**
     * PATCH:"todo/{id}/check"
     */
    public function checkAjaxAction($id, Request $request)
    {
        $checked = $request->get('checked');
        if (is_null($checked)) {
            $this->app->abort(400, "パラメーターエラー");
        }

        $todo = $this->repository->findById($id);
        if (is_null($todo)) {
            $this->app->abort(404, "TODOはありません");
        }
        if ($checked) {
            $todo->check();
        } else {
            $todo->uncheck();
        }
        $this->repository->syncCheckStatus($todo);

        return $this->app->json($todo);
    }

    /**
     * DELETE:"todo/{id}"
     */
    public function removeAction($id)
    {
        $todo = $this->repository->findById($id);
        if (is_null($todo)) {
            $this->app->abort(404, "TODOはありません");
        }
        $this->repository->remove($todo);
        return $this->app->redirect('/');
    }
}