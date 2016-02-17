<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 16/02/16
 * Time: 12:53
 */

namespace App;


use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AppRouteCollection
{
    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * AppRouteCollection constructor.
     */
    public function __construct()
    {
        $routes = new RouteCollection();
        $this->routes = $routes;
    }
}