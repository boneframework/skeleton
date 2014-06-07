<?php

namespace Bone\Mvc;
use Bone\Mvc\Router\Route;
use Bone\Regex;

class Router
{
    private $request;
    private $controller;
    private $action;
    private $params;
    private $routes;


    /**
     *  We be needin' t' look at th' map
     *  @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->uri = $request->getURI();
        $this->controller = 'index';
        $this->action = 'index';
        $this->params = array();
        $this->routes = array();

        // get th' path 'n' query string from url
        $parse = parse_url($this->uri);
        $this->uri = $parse['path'];

    }


    /**
     *  Figger out where we be goin'
     */
    private function parseRoute()
    {
        // which way be we goin' ?
        $path = $this->uri;

        // Has th'route been set?
        if ($path != '/')
        {
            // we be checkin' our instruction fer configgered routes
            $configgeration = Registry::ahoy()->get('routes');

            // stick some voodoo pins in the map
            foreach($configgeration as $route => $options)
            {
                // add the route t' the map
                $this->routes[] = new Route($route,$options);
            }

            // try an' match each route with th' uri
            $match = false;
            /** @var \Bone\Mvc\Router\Route $route */
            foreach($this->routes as $route)
            {
                // if the regex ain't for the home page an' it matches our route
                if($route->getRegexStrings()[0] != '\/' && $matches = $route->checkRoute($this->uri))
                {
                    // Garrr me hearties! It be a custom route from th' configgeration!
                    $match = true;
                    $this->controller = $route->getControllerName();
                    $this->action = $route->getActionName();
                    $this->params = $route->getParams();
                }
            }
            if(!$match)
            {
                /**
                 * not a configgered route then
                 * probably a standard controller/action/var/val/etc route then?
                 */
                $regex = new Regex(Regex\Url::CONTROLLER_ACTION_VARS);
                if($matches = $regex->getMatches($this->uri))
                {
                    // we have a controller action var val match Cap'n!
                    // settin' the destination controller and action and params
                    $match = true;
                    $this->controller = $matches['controller'];
                    $this->action = $matches['action'];
                    $ex = explode('/',$matches['varvalpairs']);
                    for($x = 0; $x <= count($ex)-1 ; $x += 2)
                    {
                        $this->params[$ex[$x]] = $ex[$x+1];
                    }
                }
                if(!$match)
                {
                    $regex = new Regex(Regex\Url::CONTROLLER_ACTION);
                    if($matches = $regex->getMatches($this->uri))
                    {
                        // we have a controller action match Cap'n!
                        // settin' the destination controller and action and params
                        $match = true;
                        $this->controller = $matches['controller'];
                        $this->action = $matches['action'];

                    }
                }
                if(!$match)
                {
                    $regex = new Regex(Regex\Url::CONTROLLER);
                    if($matches = $regex->getMatches($this->uri))
                    {
                        // we have a controller action match Cap'n!
                        // settin' the destination controller and action and params
                        $match = true;
                        $this->controller = $matches['controller'];
                        $this->action = 'index';

                    }
                }
                if(!$match)
                {
                    // theres nay route that matches cap'n
                    // settin' the destination controller and action and params
                    $this->controller = 'error';
                    $this->action = 'not-found';
                }
            }


            // be addin' the $_GET an' $_POST t' th' params!
            $method = $this->request->getMethod();
            if($method == "POST")
            {
                $this->params = array_merge($this->params, $this->request->getPost());
            }
            $this->params = array_merge($this->params, $this->request->getGet());
        }
        else
        {
            //it be the home page
            $home_page = Registry::ahoy()->get('routes')['/'];
            $this->controller = $home_page['controller'];
            $this->action = $home_page['action'];
            $this->params = $home_page['params'];
        }
        echo $this->uri.'<br />';
        echo $this->controller.' controller and '.$this->action.' action.<br />';
        echo 'Params:';
        var_dump($this->params);
    }



    public function dispatch()
    {
        $this->parseRoute();
        $controller = '\App\Controller\\'.ucwords($this->controller).'Controller';
        $action = $this->action.'Action';
        $this->request->setController($controller);
        $this->request->setAction($this->action);
        $this->request->setParams($this->params);
        if(!class_exists($controller))
        {
            $controller = '\App\Controller\ErrorController';
            $action = 'errorAction';
            $dispatch = new $controller($this->request);
        }
        else
        {
            $dispatch = new $controller($this->request);
            if(!method_exists($dispatch,$action))
            {
                $controller = '\App\Controller\ErrorController';
                $this->action = 'errorAction';
                /** @var Controller $dispatch  */
                $dispatch = new $controller($this->request);
            }
        }

        $dispatch->init();
        $dispatch->$action();
        $dispatch->postDispatch();
        /** @var \stdClass $view_vars  */
        $view_vars = (array) $dispatch->view;
        $view = $this->controller.'/'.$this->action.'.twig';
        $response_body = $dispatch->getTwig()->render($view, $view_vars);

        return new Response($response_body);
    }

}