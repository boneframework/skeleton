<?php

namespace App\Controller;

use Bone\Controller\Controller;
use Bone\Http\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController extends Controller
{
    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $body = $this->view->render('app::index');

        return new HtmlResponse($body);
    }

    public function learn(ServerRequestInterface $request) : ResponseInterface
    {
        $body = $this->view->render('app::learn');

        return new HtmlResponse($body);
    }
}
