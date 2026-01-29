<?php

namespace Tests\Unit\App\Controller;

use Barnacle\Container;
use Bone\Contracts\Service\TranslatorInterface;
use Bone\Controller\Init;
use Bone\Http\Response\HtmlResponse;
use Bone\Router\Router;
use Bone\Server\SiteConfig;
use Bone\View\ViewEngine;
use Codeception\Test\Unit;
use App\Controller\IndexController;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;

class IndexControllerTest extends Unit
{
    protected IndexController $controller;

    protected function _before(): void
    {
        $container = new Container();

        $router = new Router();
        $view = $this->getMockBuilder(ViewEngine::class)->getMock();
        $view->expects($this->any())->method('render')->willReturn('x');
        $translator = $this->getMockBuilder(TranslatorInterface::class)->getMock();
        $site = $this->getMockBuilder(SiteConfig::class)->disableOriginalConstructor()->getMock();

        $container[Router::class] = $router;
        $container[SiteConfig::class] = $site;
        $container[TranslatorInterface::class] = $translator;

        $view = $this->make(ViewEngine::class, ['render' => function() {
            return 'rendered content';
        }]);
        $container[ViewEngine::class] = $view;
        $this->controller = new IndexController();
        $this->controller = Init::controller($this->controller, $container);
    }

    protected function _after(): void
    {
        unset($this->controller);
    }

    public function testIndexAction(): void
    {
        $this->assertInstanceOf(HtmlResponse::class, $this->controller->index(new ServerRequest([], [], new Uri('/')), []));
    }
}
