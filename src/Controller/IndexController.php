<?php

namespace App\Controller;

use Bone\Controller\Controller;
use Bone\Http\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController extends Controller
{
    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $body = $this->view->render('app::index');

        return new HtmlResponse($body);
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     */
    public function learn(ServerRequestInterface $request) : ResponseInterface
    {
        $body = $this->view->render('app::learn');

        return new HtmlResponse($body);
    }
}
